<?php

namespace App\Http\Controllers;

use App\Helper;
use FileUploader;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Models\MediaMessages;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Typography\FontFactory;

class UploadMediaMessageController extends Controller
{

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->path = config('path.messages');
	}

	/**
	 * submit the form
	 *
	 * @return void
	 */
	public function store()
	{
		$publicPath = public_path('temp/');
		$file = strtolower(auth()->id() . uniqid() . time() . str_random(20));

		if (config('settings.video_encoding') == 'off') {
			$extensions = ['png', 'jpeg', 'jpg', 'gif', 'ief', 'video/mp4', 'audio/x-matroska', 'audio/mpeg'];
		} else {
			$extensions = [
				'png',
				'jpeg',
				'jpg',
				'gif',
				'ief',
				'video/mp4',
				'video/quicktime',
				'video/3gpp',
				'video/mpeg',
				'video/x-matroska',
				'video/x-ms-wmv',
				'video/vnd.avi',
				'video/avi',
				'video/x-flv',
				'audio/x-matroska',
				'audio/mpeg'
			];
		}

		// initialize FileUploader
		$FileUploader = new FileUploader('media', array(
			'limit' => config('settings.maximum_files_post'),
			'fileMaxSize' => floor(config('settings.file_size_allowed') / 1024),
			'extensions' => $extensions,
			'title' => $file,
			'uploadDir' => $publicPath
		));

		// upload
		$upload = $FileUploader->upload();

		if ($upload['isSuccess']) {

			foreach ($upload['files'] as $key => $item) {
				$upload['files'][$key] = [
					'extension' => $item['extension'],
					'format' => $item['format'],
					'name' => $item['name'],
					'size' => $item['size'],
					'size2' => $item['size2'],
					'type' => $item['type'],
					'uploaded' => true,
					'replaced' => false
				];

				switch ($item['format']) {
					case 'image':
						$this->resizeImage($item['name'], $item['extension']);
						break;

					case 'video':
						$this->uploadVideo($item['name']);
						break;

					case 'audio':
						$this->uploadMusic($item['name']);
						break;
				}
			} // foreach

		} // upload isSuccess

		return response()->json($upload);
	}

	/**
	 * Resize image and add watermark
	 *
	 * @return void
	 */
	protected function resizeImage($image, $extension)
	{
		$fileName = $image;
		$image = public_path('temp/') . $image;
		$img   = Image::read($image);
		$token = str_random(150) . uniqid() . now()->timestamp;
		$url   = ucfirst(Helper::urlToDomain(url('/')));
		$username = auth()->user()->username;

		$width     = $img->width();
		$height    = $img->height();

		if ($extension == 'gif') {
			$this->insertImage($fileName, $width, $height, $token);

			// Move file to Storage
			$this->moveFileStorage($fileName, $this->path);
		} else {
			//=============== Image Large =================//
			$scale = $width > 2000 ? 2000 : $width;

			$img = $img->scale(width: $scale);

			$fontSize = max(12, round($img->width() * 0.03));

			if (config('settings.watermark') == 'on') {
				$img->text($url . '/' . $username, $img->width() - 30, $img->height() - 30, function (FontFactory $font)
				use ($fontSize) {
					$font->filename(public_path('webfonts/arial.TTF'));
					$font->size($fontSize);
					$font->color('#eaeaea');
					$font->stroke('000000', 1);
					$font->align('right');
					$font->valign('bottom');
				});
			}

			$img->save();

			// Insert in Database
			$this->insertImage($fileName, $width, $height, $token);

			// Move file to Storage
			$this->moveFileStorage($fileName, $this->path);
		}
	}


	/**
	 * Insert Image to Database
	 *
	 * @return void
	 */
	protected function insertImage($image, $width, $height, $token)
	{
		MediaMessages::create([
			'messages_id' => 0,
			'type' => 'image',
			'file' => $image,
			'width' => $width,
			'height' => $height,
			'file_name' => '',
			'file_size' => '',
			'token' => $token,
			'status' => 'pending',
			'created_at' => now()
		]);
	}

	/**
	 * Upload Video
	 *
	 * @return void
	 */
	protected function uploadVideo($video)
	{
		$token = str_random(150) . uniqid() . now()->timestamp;

		// We insert the file into the database with a status 'pending'
		MediaMessages::create([
			'messages_id' => 0,
			'type' => 'video',
			'file' => $video,
			'video_poster' => '',
			'file_name' => '',
			'file_size' => '',
			'token' => $token,
			'status' => 'pending',
			'created_at' => now()
		]);

		// Move file to Storage
		if (config('settings.video_encoding') == 'off') {
			$this->moveFileStorage($video, $this->path);
		}
	}

	/**
	 * Upload Music
	 *
	 * @return void
	 */
	protected function uploadMusic($music)
	{
		$token = str_random(150) . uniqid() . now()->timestamp;

		// We insert the file into the database with a status 'pending'
		MediaMessages::create([
			'messages_id' => 0,
			'type' => 'music',
			'file' => $music,
			'file_name' => '',
			'file_size' => '',
			'token' => $token,
			'status' => 'pending',
			'created_at' => now()
		]);

		// Move file to Storage
		$this->moveFileStorage($music, $this->path);
	}

	/**
	 * Move file to Storage
	 *
	 * @return void
	 */
	protected function moveFileStorage($file, $path)
	{
		$localFile = public_path('temp/' . $file);

		// Move the file...
		Storage::putFileAs($path, new File($localFile), $file);

		// Delete temp file
		unlink($localFile);
	}

	/**
	 * delete a file
	 *
	 * @return void
	 */
	public function delete()
	{
		$path  = $this->path;
		$media = MediaMessages::whereFile($this->request->file)->first();

		if ($media) {

			$localFile = 'temp/' . $media->file;

			Storage::delete($path . $media->file);
			Storage::delete($path . $media->video_poster);

			// Delete local file (if exist)
			Storage::disk('default')->delete($localFile);

			$media->delete();
		}

		return response()->json([
			'success' => true
		]);
	}

}
