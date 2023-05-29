{{-- Body --}}
<x-mail::message>
    Тема - {{ $mail->theme }} <br>
    Username - {{ $mail->username }} <br>
    Компания - {{ $mail->company }} <br>
    Телефон - {{ $mail->phone }} <br>
    Teкст - {{ $mail->text }} <br>
</x-mail::message>
