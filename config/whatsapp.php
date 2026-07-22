<?php

return [
    'order_message_template' => "*PANCALABA*\n"
        . "_Bon Reparasi Pelanggan_\n\n"
        . "Halo {nama_pelanggan},\n\n"
        . "Berikut detail bon reparasi Anda:\n"
        . "No. Bon: *{no_bon}*\n"
        . "Tanggal Transaksi: {tanggal_transaksi}\n\n"
        . "Link Bon:\n{link_bon}\n\n"
        . "Mohon simpan pesan ini sebagai bukti transaksi.\n\n"
        . "Terima kasih\n"
        . "{nama_kasir} Pancalaba",
];
