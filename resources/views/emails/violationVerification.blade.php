<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Peringatan Deteksi Pelanggaran</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        padding: 20px;
        margin: 0;
      }
      .email-container {
        max-width: 600px;
        margin: auto;
        background-color: #ffffff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
      }
      .header {
        text-align: center;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
        margin-bottom: 20px;
      }
      .header h2 {
        margin: 0;
        color: #007bff;
      }
      .content {
        font-size: 16px;
        color: #333;
        line-height: 1.6;
      }
      .content img {
        width: 100%;
        height: 300px;
        object-fit: contain;
        border-radius: 5px;
        margin: 20px 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }
      .details {
        margin: 25px 0;
      }
      .details table {
        width: 100%;
        border-collapse: collapse;
        background-color: #f8f9fa;
        border-radius: 5px;
      }
      .details th,
      .details td {
        text-align: left;
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
      }
      .details th {
        background-color: #e9ecef;
        font-weight: 600;
        width: 40%;
      }
      .button-container {
        text-align: center;
        margin: 25px 0;
      }
      .button-container a {
        display: inline-block;
        padding: 12px 24px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s ease;
      }
      .button-container a:hover {
        background-color: #0056b3;
      }
      .footer {
        text-align: center;
        font-size: 12px;
        color: #6c757d;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
      }
    </style>
  </head>
  <body>
    <div class="email-container">
      <div class="header">
        <h2>Pelanggaran Lalu Lintas Terdeteksi</h2>
        <p>Sistem Informasi Pelanggaran Lalu Lintas</p>
      </div>
      <div class="content">
        <p>Yth. Bapak/Ibu Pemilik Kendaraan,</p>
        <p>Kendaraan Anda telah terdeteksi melakukan pelanggaran lalu lintas. Berikut adalah rinciannya:</p>

        <img src="https://placehold.co/500x300?text=Foto+Bukti" alt="Foto Bukti Pelanggaran" />

        <div class="details">
          <table>
            <tr>
              <th>Nomor Plat</th>
              <td>{{$ticket->violation->number}}</td>
            </tr>
            <tr>
              <th>Jenis Pelanggaran</th>
              <td>{{$ticket->violation->violationType->name}}</td>
            </tr>
            <tr>
              <th>Tanggal & Waktu</th>
              <td>{{$ticket->violation->created_at}}</td>
            </tr>
            <tr>
              <th>Lokasi</th>
              <td>{{$ticket->violation->camera->location}}</td>
            </tr>
            <tr>
              <th>Penindak</th>
              <td>{{$ticket->investigator->name}}</td>
            </tr>
          </table>
        </div>

        <div class="button-container">
          <a href="https://www.etilang.web.id" target="_blank">Konfirmasi Pelanggaran</a>
        </div>

        <p><strong>Catatan:</strong> Mohon tinjau pelanggaran ini untuk tindakan lebih lanjut dan pemrosesan dalam sistem.</p>
      </div>

      <div class="footer">
        <p>Ini adalah pesan otomatis dari Sistem Deteksi Pelanggaran. Jangan membalas email ini.</p>
      </div>
    </div>
  </body>
</html>
