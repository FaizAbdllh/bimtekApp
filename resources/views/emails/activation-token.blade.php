<p>Yth. {{ $user->name }},</p>
<p>Anda diundang mengikuti pelatihan: <strong>{{ $bimtek->judul }}</strong>.</p>
<p>Silakan aktifkan akun Anda dengan mengunjungi tautan berikut:</p>
<p><a href="{{ url('/activate/'.$token) }}">{{ url('/activate/'.$token) }}</a></p>
<p>Token ini berlaku selama 7 hari dan hanya dapat dipakai satu kali.</p>
<p>Jika Anda tidak melakukan pendaftaran, abaikan email ini.</p>
<p>Salam,<br>Tim BBPMP</p>
