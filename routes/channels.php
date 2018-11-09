<?php
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    Log::debug('notification channel 1: ' . $user->id . ' : ' . $id);
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user.job.{userId}', function ($user, $userId) {
    Log::debug('notification channel 2: ' . $user->id . ' : ' . $userId);
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('users.{userId}', function ($user, $userId) {
    Log::debug('notification channel 3: ' . $user->id . ' : ' . $userId);
    return $user->id === (int) $userId;
});

Broadcast::channel('job.image.{jobId}', function ($user, $jobId) {
    Log::debug('notification channel 4: ' . $user->id . ' : ' . $jobId);
    $job = Job::find($jobId);
    if ($job->contractorId === $user->id) {
        return true;
    } else if ($job->customerId === $user->id) {
        return true;
    } else {
        foreach ($job->jobTasks()->get() as $key => $jobTask) {
            if ($jobTask->contractor_id === $user->id) {
                return true;
            }
        }
    }

    return false;
});