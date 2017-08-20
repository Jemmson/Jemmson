<h1>Hello,</h1>
<p>{{ $contractor }} has initated a bid.</p>
<p>Job Name: {{ $job_name }}</p>
<label for="">View the bid here: {{ url('/login/' . $link . '/' . $job_id) }}</label>
