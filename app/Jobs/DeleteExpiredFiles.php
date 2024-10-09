<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Support\Facades\Storage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class DeleteExpiredFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function handle()
    {

        if ($this->message && $this->message->file_path) {

            $filePaths = json_decode($this->message->file_path, true);

            if (is_array($filePaths)) {
                foreach ($filePaths as $filePath) {
                    if (Storage::disk('public')->exists($filePath) && Carbon::now()->diffInMinutes($this->message->created_at) >= 55) {
                        Storage::disk('public')->delete($filePath);
                    } else {
                        \Log::warning('File does not exist or is not old enough: ' . $filePath);
                    }
                }

                $this->message->update([
                    'file_path' => null,
                    'file_type' => null,
                    'file_name' => null,
                ]);
            }
        } else {
            \Log::warning('Message not found or file path is empty for job.');
        }
    }
}
