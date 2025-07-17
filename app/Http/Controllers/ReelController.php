<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Models\Reel;
use App\Models\MediaReel;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Jobs\EncodeVideoReel;
use App\Services\CoconutVideoService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReelController extends Controller
{
    public function create()
    {
        abort_if(auth()->user()->verified_id != 'yes' || !config('settings.allow_reels'), 404);

        return view('users.create-reel');
    }

    public function store(Request $request)
    {
        $fileuploader = $request->input('fileuploader-list-media');
        $fileuploader = json_decode($fileuploader, TRUE);

        if (!$fileuploader) {
            return response()->json([
                'success' => false,
                'errors' => ['error' => __('general.please_select_video')],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ]);
        } //<-- Validator

        $reel = Reel::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'type' => $request->type,
        ]);

        // Update status Media (Image/Video)
        if ($fileuploader) {
            MediaReel::whereName($fileuploader[0]['file'])
                ->update([
                    'reels_id' => $reel->id,
                    'status' => true,
                    'duration_video' => $request->duration ? Helper::getDurationInMinutes($request->duration) : null,
                ]);
        }

        $video = MediaReel::whereReelsId($reel->id)->first();

        if ($video && config('settings.video_encoding') == 'on') {
            try {
                if (config('settings.encoding_method') == 'ffmpeg') {
                    $this->dispatch(new EncodeVideoReel($video));
                } else {
                    CoconutVideoService::handle($video, 'reel');
                }

                // Change status Pending to Encode
                Reel::whereId($reel->id)->update([
                    'status' => 'encode'
                ]);

                return response()->json([
                    'success' => true,
                    'encode' => true
                ]);
            } catch (\Exception $e) {
                info('Error creating Reel: ' . $e->getMessage());
            }
        } else {
            $reel->update([
                'status' => 'active'
            ]);

            $this->moveFileStorage($video->name, config('path.reels'));

            if ($request->video_thumbnail) {
                $this->moveFileStorage($request->video_thumbnail, config('path.reels'));

                $video->update([
                    'video_poster' => $request->video_thumbnail
                ]);
            }

            return response()->json([
                'success' => true,
                'url' => route('reels.section.show', $reel->id)
            ]);
        }
    }

    protected function moveFileStorage($file, $path): void
    {
        $localFile = public_path('temp/' . $file);

        // Move the file...
        Storage::putFileAs($path, new File($localFile), $file);

        // Delete temp file
        unlink($localFile);
    }

    public function destroy($id)
    {
        $pathReels = config('path.reels');
        $reel = Reel::with(['media'])->whereUserId(auth()->id())->whereId($id)->firstOrFail();

        //Delete Media
        Storage::delete($pathReels . $reel->media->name);
        Storage::delete($pathReels . $reel->media->video_poster);
        $reel->media->delete();
        //Delete Reel
        $reel->delete();

        return back()->withSuccessMessage(__('general.successfully_removed'));
    }

    public function incrementView(Request $request, $id)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $reel = Reel::find($id);

        if ($reel && $reel->user_id != auth()->id() && !auth()->user()->isSuperAdmin()) {
            $reel->increment('views');
        }
    }

    /*
     * Show a single reel
    */
    public function show($id)
    {
        $singleReel = auth()->user()->singleReel($id);
        $reels = auth()->user()->reels($id);

        return view('reels.reels', [
            'reels' => $reels,
            'reelSingle' => $singleReel
        ]);
    }

    /*
     * Show all reels
    */
    public function showAll()
    {
        $reels = auth()->user()->reels();

        if ($reels->count() == 0) {
            return redirect()->route('home');
        }

        return view('reels.reels', compact('reels'));
    }

    /**
     *  Load more reels via AJAX
     */
    public function loadMore(Request $request)
    {
        if (!$request->ajax()) {
            return redirect()->route('reels.index');
        }

        $reels = auth()->user()->reels();

        // Check if there are more reels available
        $hasMore = $reels->hasMorePages();

        // Transform the data to send in JSON format
        $formattedReels = [
            'reels' => $reels->map(function ($reel) {
                return [
                    'id' => $reel->id,
                    'media' => [
                        'video' => Helper::getFile(config('path.reels') . $reel->media->name),
                        'video_poster' => Helper::getFile(config('path.reels') . $reel->media->video_poster),
                        'duration_video' => $reel->media->duration_video
                    ],
                    'user' => [
                        'name' => $reel->user->name,
                        'avatar' => Helper::getFile(config('path.avatar') . $reel->user->avatar)
                    ]
                ];
            })->toArray()
        ];

        return response()->json([
            'reels' => $formattedReels,
            'has_more' => $hasMore
        ]);
    }

    public function update(Request $request, $id)
    {
        $reel = Reel::whereUserId(auth()->id())->whereId($id)->firstOrFail();

        $request->validate([
            'title' => 'max:100'
        ]);

        $reel->update([
            'title' => $request->title
        ]);

        return back()->withSuccessMessage(__('admin.success_update'));
    }
}
