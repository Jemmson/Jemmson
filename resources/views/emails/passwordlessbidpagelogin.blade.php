<h1>Link Sent to you!</h1>
<p>Your email: {{ $email }}</p>
<p>Job Name: {{ $job_name }}</p>
<label for="">Link: {{ url('/login/' . $link . '/' . $job_id) }}</label>
