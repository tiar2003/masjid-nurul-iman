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

let dataAmal = {};

function formatTanggalDisplay(tanggal) {
    const parts = tanggal.split("-");
    return `${parts[2]}-${parts[1]}-${parts[0]}`;
}


function formatRupiah(angka) {
    return angka.toLocaleString("id-ID");
}

async function loadData() {
    const res = await fetch("api.php");
    const json = await res.json();

    dataAmal = {};

    json.forEach(row => {
        if (row.nominal && row.nominal != 0) {
            dataAmal[row.tanggal] = parseInt(row.nominal);
        }
    });

    renderTable();
}

function renderTable() {

    const table = document.getElementById("dataTable");
    table.innerHTML = "";

    let runningTotal = 0;
    let grandTotal = 0;

    tanggalTetap.forEach((item, index) => {

        const hari = item[0];
        const tanggal = item[1];
        const nilai = dataAmal[tanggal] || null;

        if (nilai !== null) {
            runningTotal += nilai;
            grandTotal += nilai;
        }

        table.innerHTML += `
            <tr>
                <td>${index + 1}</td>
                <td>${hari}</td>
                <td>${formatTanggalDisplay(tanggal)}</td>
        <td>
<input 
    type="text"
    inputmode="numeric"
    pattern="[0-9]*"
    value="${nilai !== null ? formatRupiah(nilai) : ''}"
    oninput="formatInput(this)"
    onblur="autoSave(this,'${tanggal}')"
>
</td>

                <td>${nilai !== null ? "Rp " + formatRupiah(runningTotal) : ""}</td>
            </tr>
        `;
    });

    document.getElementById("grandTotal").innerText =
        grandTotal ? formatRupiah(grandTotal) : "0";
}

function formatInput(input) {

    let value = input.value.replace(/\D/g, '');

    if (value === "") {
        input.value = "";
        return;
    }

    input.value = parseInt(value).toLocaleString("id-ID");
}


async function handleEnter(e, tanggal) {

    if (e.key === "Enter") {

        let value = e.target.value.replace(/\D/g, '');

        if (value === "") {
            await fetch("api.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `tanggal=${tanggal}&nominal=`
            });
            loadData();
            return;
        }

        // 🔥 Jika 3 digit atau kurang → kali 1000
        if (value.length <= 3) {
            value = parseInt(value) * 1000;
        }

        value = parseInt(value);

        await fetch("api.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `tanggal=${tanggal}&nominal=${value}`
        });

        loadData();
    }
}

let saveTimeout = {};

function autoSave(input, tanggal) {

    clearTimeout(saveTimeout[tanggal]);

    saveTimeout[tanggal] = setTimeout(async () => {

        let value = input.value.replace(/\D/g, '');

        if (value === "") {
            await fetch("api.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `tanggal=${tanggal}&nominal=`
            });
            loadData();
            return;
        }

        // jika 3 digit → x1000
        if (value.length <= 3) {
            value = parseInt(value) * 1000;
        }

        value = parseInt(value);

        await fetch("api.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `tanggal=${tanggal}&nominal=${value}`
        });

        loadData();

    }, 800); // tunggu 0.8 detik setelah berhenti mengetik
}


async function saveValue(input, tanggal) {

    let value = input.value;

    if (value === "") {
        await fetch("api.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `tanggal=${tanggal}&nominal=`
        });
        loadData();
        return;
    }

    // jika 3 digit → kali 1000
    if (value.length <= 3) {
        value = parseInt(value) * 1000;
    }

    await fetch("api.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `tanggal=${tanggal}&nominal=${value}`
    });

    loadData();
}


async function resetData() {

    if(!confirm("Yakin reset semua data?")) return;

    await fetch("reset.php");

    loadData();
}


function printTable() {
    window.print();
}

function exportExcel() {

    let table = `
        <table border="1">
        <tr>
            <th>No</th>
            <th>Hari</th>
            <th>Tanggal</th>
            <th>Hasil Kotak Amal</th>
            <th>Jumlah</th>
        </tr>
    `;

    let runningTotal = 0;
    let nomor = 1;

    tanggalTetap.forEach((item) => {

        const hari = item[0];
        const tanggal = item[1];
        const nilai = dataAmal[tanggal] || null;

        if (nilai === null) return; // skip jika kosong

        runningTotal += nilai;

        table += `
            <tr>
                <td>${nomor++}</td>
                <td>${hari}</td>
                <td>${tanggal}</td>
                <td>${nilai}</td>
                <td>${runningTotal}</td>
            </tr>
        `;
    });

    table += `</table>`;

    let blob = new Blob([table], {
        type: "application/vnd.ms-excel"
    });

    let url = URL.createObjectURL(blob);
    let a = document.createElement("a");
    a.href = url;
    a.download = "Kotak_Amal_Terawih_2026.xls";
    a.click();

    URL.revokeObjectURL(url);
}



loadData();
