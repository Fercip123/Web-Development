function cekHargaKamar(kodeKamar) {
  switch (kodeKamar) {
    case "Ekonomi":
      return 70000;
    case "VIP":
      return 100000;
    case "VVIP":
      return 150000;
    default:
      return 0;
  }
}

function hitungTotalBayar(lamaMenginap, hargaKamar) {
  return lamaMenginap * hargaKamar;
}

document.getElementById("hitungBtn").addEventListener("click", function () {
  const noPasien = document.getElementById("noPasien").value;
  const namaPasien = document.getElementById("namaPasien").value;
  const kodeKamar = document.getElementById("kodeKamar").value;
  const lamaRawatInap = parseInt(
    document.getElementById("lamaRawatInap").value
  );

  if (
    !noPasien ||
    !namaPasien ||
    !kodeKamar ||
    isNaN(lamaRawatInap) ||
    lamaRawatInap <= 0
  ) {
    alert("Mohon isi semua data dengan benar!");
    return;
  }

  const hargaKamar = cekHargaKamar(kodeKamar);

  const totalBayar = hitungTotalBayar(lamaRawatInap, hargaKamar);

  const dataPasien = {
    noPasien: noPasien,
    namaPasien: namaPasien,
    kodeKamar: kodeKamar,
    lamaRawatInap: lamaRawatInap,
    hargaKamarPerHari: hargaKamar,
    totalPembayaran: totalBayar,
  };

  document.getElementById(
    "totalBayar"
  ).value = `Rp. ${dataPasien.totalPembayaran.toLocaleString("id-ID")}`;

  const outputDiv = document.getElementById("outputData");
  outputDiv.innerHTML = `
        <h3>Data Transaksi</h3>
        <p><strong>No. Pasien:</strong> ${dataPasien.noPasien}</p>
        <p><strong>Nama Pasien:</strong> ${dataPasien.namaPasien}</p>
        <p><strong>Kode Kamar:</strong> ${dataPasien.kodeKamar}</p>
        <p><strong>Harga Kamar/Hari:</strong> Rp. ${dataPasien.hargaKamarPerHari.toLocaleString(
          "id-ID"
        )}</p>
        <p><strong>Lama Rawat Inap:</strong> ${
          dataPasien.lamaRawatInap
        } hari</p>
        <p><strong>Total Pembayaran:</strong> Rp. ${dataPasien.totalPembayaran.toLocaleString(
          "id-ID"
        )}</p>
    `;
});
