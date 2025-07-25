<?php

namespace App\Jobs;

use App\Models\Updates;
use App\Events\NewPostEvent;
use App\Models\AdminSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PostScheduled implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $posts = Updates::select(['id', 'scheduled_date', 'status'])
            ->whereStatus('schedule')
            ->where('scheduled_date', '<=', now())
            ->get();

        foreach ($posts as $post) {
            $post->update([
                'date' => $post->scheduled_date,
                'status' => 'active'
            ]);

            if (!AdminSettings::value('disable_new_post_notification')) {
                // Send notification New Post
                event(new NewPostEvent($post));
            }
        }
    }
}
