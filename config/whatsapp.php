<?php

return [
    'order_message_template' => "*PANCALABA*\n"
        . "*Bon Reparasi Pelanggan*\n\n"
        . "Halo {nama_pelanggan},\n\n"
        . "Berikut detail bon reparasi Anda:\n"
        . "No. Bon: *{no_bon}*\n"
        . "Estimasi Selesai: {estimasi_selesai}\n\n"
        . "Link Bon:\n{link_bon}\n\n"
        . "Mohon simpan pesan ini sebagai bukti transaksi.\n\n"
        . "Terima kasih\n"
        . "Pancalaba",

    'pickup_reminder_template' => "Halo {nama_pelanggan}, barang yang direparasi di PANCA LABA "
        . "dengan Nomor Bon {no_bon} telah selesai direparasi dan siap diambil / diantar.\n"
        . "Khusus Kelapa Gading gratis pengantaran.\n\n"
        . "Note: harap menyimpan nomor ini agar kami dapat mengirim update proses pengerjaan, "
        . "serta membantu layanan pick up & delivery.\n\n"
        . "Terima kasih 😊\n\n"
        . "PANCA LABA",
];
