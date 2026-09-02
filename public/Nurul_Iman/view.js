const tanggalTetap = [
["RABU","2026-02-18"],
["KAMIS","2026-02-19"],
["JUMAT","2026-02-20"],
["SABTU","2026-02-21"],
["AHAD","2026-02-22"],
["SENIN","2026-02-23"],
["SELASA","2026-02-24"],
["RABU","2026-02-25"],
["KAMIS","2026-02-26"],
["JUMAT","2026-02-27"],
["SABTU","2026-02-28"],
["AHAD","2026-03-01"],
["SENIN","2026-03-02"],
["SELASA","2026-03-03"],
["RABU","2026-03-04"],
["KAMIS","2026-03-05"],
["JUMAT","2026-03-06"],
["SABTU","2026-03-07"],
["AHAD","2026-03-08"],
["SENIN","2026-03-09"],
["SELASA","2026-03-10"],
["RABU","2026-03-11"],
["KAMIS","2026-03-12"],
["JUMAT","2026-03-13"],
["SABTU","2026-03-14"],
["AHAD","2026-03-15"],
["SENIN","2026-03-16"],
["SELASA","2026-03-17"],
["RABU","2026-03-18"]
];

function formatRupiah(angka) {
    return angka.toLocaleString("id-ID");
}

function formatTanggalDisplay(tanggal) {
    const parts = tanggal.split("-");
    return `${parts[2]}-${parts[1]}-${parts[0]}`;
}


async function loadData() {

    const res = await fetch("api.php");
    const json = await res.json();

    // ubah data database jadi object
    let dataMap = {};
    json.forEach(row => {
        if (row.nominal !== null && row.nominal !== "") {
            dataMap[row.tanggal] = parseInt(row.nominal);
        }
    });

    const table = document.getElementById("dataTable");
    table.innerHTML = "";

    let runningTotal = 0;
    let grandTotal = 0;
    let nomor = 1;

    tanggalTetap.forEach((item) => {

        const hari = item[0];
        const tanggal = item[1];
        const nilai = dataMap[tanggal] || null;

        // 🔥 SKIP jika belum ada isi
        if (nilai === null) return;

        runningTotal += nilai;
        grandTotal += nilai;

        table.innerHTML += `
            <tr>
                <td>${nomor++}</td>
                <td>${hari}</td>
                <td>${formatTanggalDisplay(tanggal)}</td>
                <td>Rp ${formatRupiah(nilai)}</td>
                <td>Rp ${formatRupiah(runningTotal)}</td>
            </tr>
        `;
    });

    document.getElementById("grandTotal").innerText =
        grandTotal ? formatRupiah(grandTotal) : "0";
}

loadData();
setInterval(loadData, 2000);
