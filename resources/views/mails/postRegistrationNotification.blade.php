<x-mail::message>
# Dear {{ $name }},

@if ($payment_type=='zelle')

We thank you for your interest to be a part of ATMIYA Core Mission to educate, empower, enrich and elevate community members through financial education and enterprenurial networking opportunities.
<br><br>
We have received your application for **{{$membership_category}}**.
<br><br>
Please use the email **{{$zelle_payment_email}}** to pay membership fee.
<br><br>
Our membership team will review your application as soon as the receipt of membership fee payment and we will inform you once the review process is complete.

@else

We thank you for your interest to be a part of ATMIYA Core Mission to educate, empower, enrich and elevate community members through financial education and enterprenurial networking opportunities.
<br><br>
We have received your application for **{{$membership_category}}**.
<br><br>
Our membership team is reviewing your application and we will inform you once the review process is complete.

@endif

Sincerely,<br>
ATMIYA Membership Committee
</x-mail::message>
