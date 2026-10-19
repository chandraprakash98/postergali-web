<h1>New PosterGali contact request</h1>

<p><strong>Name:</strong> {{ $contact['first_name'] }} {{ $contact['last_name'] }}</p>
<p><strong>Email:</strong> {{ $contact['email'] }}</p>
<p><strong>Phone:</strong> {{ $contact['phone'] }}</p>
<p><strong>Message:</strong></p>
<p>{!! nl2br(e($contact['message'])) !!}</p>
