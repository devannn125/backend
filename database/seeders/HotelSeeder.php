<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotels;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $hotels = [
            [
                'id_hotel'    => 'HTL001', // ID EKSPLISIT
                'nama_hotel'  => 'Hotel Pitulungan',
                'alamat'      => 'Jl. Malioboro No. 99, Sosromenduran',
                'kota'        => 'Yogyakarta',
                'deskripsi'   => 'Hotel bintang 4 dengan pemandangan kota dan akses langsung ke pusat perbelanjaan Malioboro. Cocok untuk liburan keluarga maupun perjalanan bisnis.',
                'rating'      => 4.5,
                'email'       => 'cs@pitulungan.inn',
                'no_hp'       => '081234567890',
                'hotel_image' => 'hotels/pitulungan_jogja.jpg',
            ],
            [
                'id_hotel'    => 'HTL002',
                'nama_hotel'  => 'Capella Ubud',
                'alamat'      => 'Jl. RY Dalem, Keliki, Tegallalang',
                'kota'        => 'Bali',
                'deskripsi'   => 'Resor tenda mewah di tengah hutan hujan Ubud yang asri, menawarkan pengalaman menginap yang eksklusif dan menyatu dengan alam.',
                'rating'      => 4.9,
                'email'       => 'info@capellaubud.com',
                'no_hp'       => '03612098888',
                'hotel_image' => 'hotels/capella_bali.jpg',
            ],
            [
                'id_hotel'    => 'HTL003',
                'nama_hotel'  => 'Grand Nusantara',
                'alamat'      => 'Jl. Thamrin No. 15, Menteng',
                'kota'        => 'Jakarta',
                'deskripsi'   => 'Hotel bisnis eksklusif di pusat kawasan SCBD Jakarta dengan fasilitas ruang pertemuan berstandar internasional.',
                'rating'      => 4.2,
                'email'       => 'hello@grandnusantara.com',
                'no_hp'       => '02155566677',
                'hotel_image' => 'hotels/nusantara_jkt.jpg',
            ],
            [
                'id_hotel'    => 'HTL004',
                'nama_hotel'  => 'Royal Ambarrukmo',
                'alamat'      => 'Jl. Laksda Adisucipto No.81, Caturtunggal',
                'kota'        => 'Yogyakarta',
                'deskripsi'   => 'Hotel bersejarah yang menggabungkan kemewahan modern dengan tradisi kerajaan Jawa yang kental.',
                'rating'      => 4.8,
                'email'       => 'reservations@royalambarrukmo.com',
                'no_hp'       => '0274488488',
                'hotel_image' => 'hotels/royal_ambarrukmo.jpg',
            ],
            [
                'id_hotel'    => 'HTL005',
                'nama_hotel'  => 'Guyana Hotel',
                'alamat'      => 'Jl. Pantai Kuta, Kec. Kuta',
                'kota'        => 'Bali',
                'deskripsi'   => 'Penginapan tropis yang nyaman dengan fasilitas kolam renang luas, hanya berjarak 5 menit jalan kaki dari Pantai Kuta.',
                'rating'      => 4.5,
                'email'       => 'booking@guyanahotel.com',
                'no_hp'       => '0361999888',
                'hotel_image' => 'hotels/guyana_bali.jpg',
            ],
            [
                'id_hotel'    => 'HTL006',
                'nama_hotel'  => 'Hotel Tentrem',
                'alamat'      => 'Jl. P. Mangkubumi No.72A, Jetis',
                'kota'        => 'Yogyakarta',
                'deskripsi'   => 'Hotel bintang 5 independen yang menawarkan ketenangan dan pelayanan istimewa di jantung kota budaya.',
                'rating'      => 4.8,
                'email'       => 'info@hoteltentrem.com',
                'no_hp'       => '02746415555',
                'hotel_image' => 'hotels/tentrem_jogja.jpg',
            ],
            [
                'id_hotel'    => 'HTL007',
                'nama_hotel'  => 'The Ritz-Carlton',
                'alamat'      => 'Jl. DR. Ide Anak Agung Gde Agung Kav.E.1.1',
                'kota'        => 'Jakarta',
                'deskripsi'   => 'Simbol kemewahan di Mega Kuningan Jakarta, menampilkan kamar-kamar elegan dengan panorama cakrawala kota yang memukau.',
                'rating'      => 4.7,
                'email'       => 'rc.jktmz.leads@ritzcarlton.com',
                'no_hp'       => '02125518888',
                'hotel_image' => 'hotels/ritzcarlton_jkt.jpg',
            ],
            [
                'id_hotel'    => 'HTL008',
                'nama_hotel'  => 'Padma Resort',
                'alamat'      => 'Jl. Padma No. 1, Legian',
                'kota'        => 'Bali',
                'deskripsi'   => 'Resor pemenang penghargaan dengan taman tropis yang rimbun dan fasilitas keluarga yang lengkap di tepi pantai Legian.',
                'rating'      => 4.8,
                'email'       => 'reservation.legian@padmahotels.com',
                'no_hp'       => '0361752111',
                'hotel_image' => 'hotels/padma_bali.jpg',
            ],
            [
                'id_hotel'    => 'HTL009',
                'nama_hotel'  => 'JW Marriott',
                'alamat'      => 'Jl. Embong Malang 85-89',
                'kota'        => 'Surabaya',
                'deskripsi'   => 'Akomodasi premium bintang 5 di pusat kawasan bisnis dan perbelanjaan Surabaya, dilengkapi dengan berbagai pilihan restoran berkelas.',
                'rating'      => 4.6,
                'email'       => 'mhrs.subjw.reservations@marriotthotels.com',
                'no_hp'       => '0315458888',
                'hotel_image' => 'hotels/jwmarriott_sub.jpg',
            ],
            [
                'id_hotel'    => 'HTL010',
                'nama_hotel'  => 'Aryaduta',
                'alamat'      => 'Jl. Sumatera No.51, Citarum',
                'kota'        => 'Bandung',
                'deskripsi'   => 'Terletak strategis di pusat kota Bandung, memberikan kemudahan akses ke pusat factory outlet dan kuliner legendaris.',
                'rating'      => 4.3,
                'email'       => 'info.bandung@aryaduta.com',
                'no_hp'       => '0224211234',
                'hotel_image' => 'hotels/aryaduta_bdg.jpg',
            ]
        ];

        foreach ($hotels as $hotelData) {
            Hotels::create($hotelData);
        }
    }
}