Dokumentasi: Masalah ICE/NAT pada Jitsi Videobridge (JVB)
Gejala
User sering mendapat pesan "You have been disconnected"
Koneksi terputus beberapa detik setelah join meeting
Penyebab
1. coturn (TURN server) tidak terinstall

dpkg -l coturn
# Status: rc  (removed, config-files only)
TURN server berfungsi sebagai fallback relay saat koneksi UDP langsung antara client dan JVB gagal. Tanpa TURN, tidak ada fallback → disconnect.

2. JVB menggunakan STUN eksternal untuk mapping IP publik

Di 
/etc/jitsi/videobridge/jvb.conf
:

hocon
# KONFIGURASI LAMA (BERMASALAH)
ice4j {
    harvest {
        mapping {
            stun {
                addresses = ["meet-jit-si-turnrelay.jitsi.net:443"]
            }
        }
    }
}
JVB bergantung pada STUN server eksternal untuk mengetahui IP publiknya sendiri. Jika STUN lambat atau gagal → ICE timeout → disconnect.

Log error yang muncul:

timeout for pair: 148.230.99.128:10000/udp/host -> client_ip:port/udp/prflx (stream.RTP), failing
EndpointConnectionStatusMonitor: endpoint is considered disconnected. No activity for PT8S
Solusi
1. Reinstall coturn

bash
apt-get install -y coturn
Tambahkan ke 
/etc/turnserver.conf
:

listening-ip=148.230.99.128
external-ip=148.230.99.128
2. Ganti STUN eksternal dengan static NAT mapping di 
/etc/jitsi/videobridge/jvb.conf

hocon
# KONFIGURASI BARU (BENAR)
ice4j {
    harvest {
        mapping {
            aws {
                enabled = false
            }
            stun {
                enabled = false
            }
            static-mappings = [
                {
                    local-address = "148.230.99.128"
                    public-address = "148.230.99.128"
                }
            ]
        }
    }
}
3. Restart service

bash
systemctl restart coturn jitsi-videobridge2
4. Verifikasi di log JVB

bash
grep -i "static mapping\|SinglePortUdp" /var/log/jitsi/jvb.log
Output yang benar:

Adding a static mapping: StaticMapping(localAddress=148.230.99.128, publicAddress=148.230.99.128)
Initialized SinglePortUdpHarvester with address 148.230.99.128:10000/udp
Catatan
Port yang harus terbuka di firewall: UDP 10000 (JVB media), UDP 3478 (TURN), TCP 5349 (TURNS)
Static mapping lebih reliable daripada STUN eksternal karena tidak bergantung pada koneksi ke server pihak ketiga
Jika server berada di belakang NAT (IP lokal ≠ IP publik), ubah local-address ke IP lokal dan public-address ke IP publik