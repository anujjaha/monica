<?php

namespace App\Models\Contact;

use Carbon\Carbon;
use App\Models\User\User;
use App\Models\Account\Account;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ModelBindingHasherWithContact as Model;

/**
 * @property Account $account
 * @property Contact $contact
 */
class ReminderOutbox extends Model
{
    protected $table = 'reminder_outbox';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'planned_date',
    ];

    /**
     * Get the account record associated with the reminder.
     *
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the reminder record associated with the reminder.
     *
     * @return BelongsTo
     */
    public function reminder()
    {
        return $this->belongsTo(Reminder::class);
    }

    /**
     * Get the user record associated with the reminder.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logSent($message)
    {
        $reminderSent = new ReminderSent;
        $reminderSent->account_id = $this->account_id;
        $reminderSent->reminder_id = $this->reminder_id;
        $reminderSent->user_id = $this->user_id;
        $reminderSent->planned_date = $this->planned_date;
        $reminderSent->sent_date = Carbon::now();
        $reminderSent->frequency_type = is_null($this->reminder) ? null : $this->reminder->frequency_type;
        $reminderSent->frequency_number = is_null($this->reminder) ? null : $this->reminder->frequency_number;
        $reminderSent->html_content = $message;
        dd($message);
        if ($event->notification instanceof UserNotified) {
            $reminderSent->nature = 'notification';
        }

        if ($event->notification instanceof UserReminded) {
            $reminderSent->nature = 'reminder';
        }
        $reminderSent->save();
    }
}
