<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Post;

class CommentCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $comment;
    public $author ;
    public $post ;
    public function __construct($comment)
    {
        $this->comment = $comment;
        $this->post = Post::findOrFail($comment->post_id);
        $this->author = User::findOrFail($this->post->user_id);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Comment on Your Post')
            ->line('A new comment has been added to your post titled "' . $this->post->title . '".')
            ->line('Comment Content: ' . $this->comment->content)
            ->action('View Post', route('posts.show', $this->post->slug)) // Adjust the route to match your slug-based routing
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
