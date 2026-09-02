document.addEventListener('DOMContentLoaded', function() {
    loadData();

    // Fungsi untuk mengambil data dari Laravel
    function loadData() {
        fetch('/api/terawih')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('table-body'); // Pastikan id di HTML Anda sesuai
                let total = 0;
                let rows = '';

                data.forEach((item, index) => {
                    total += parseInt(item.nominal);
                    rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${getHari(item.tanggal)}</td>
                            <td>${formatTanggal(item.tanggal)}</td>
                            <td>Rp ${formatRupiah(item.nominal)}</td>
                            <td>Rp ${formatRupiah(total)}</td>
                        </tr>
                    `;
                });

                if (data.length === 0) {
                    rows = '<tr><td colspan="5" style="text-align: center;">Belum ada data</td></tr>';
                }

                if (tableBody) tableBody.innerHTML = rows;

                const totalElement = document.getElementById('total-keseluruhan');
                if (totalElement) totalElement.innerText = `Total Keseluruhan: Rp ${formatRupiah(total)}`;
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    // Tangkap form submit (Jika ID form Anda 'form-terawih')
    const form = document.getElementById('form-terawih');
    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const tanggal = document.getElementById('tanggal').value;
            const nominal = document.getElementById('nominal').value;

            fetch('/api/terawih', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ tanggal: tanggal, nominal: nominal })
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'ok') {
                    loadData();
                    form.reset();
                }
            })
            .catch(error => console.error('Error saving:', error));
        });
    }

    // Tombol Reset Data (Jika ID tombol 'btn-reset')
    const btnReset = document.getElementById('btn-reset');
    if(btnReset) {
        btnReset.addEventListener('click', function() {
            if(confirm('Yakin ingin mereset semua data?')) {
                fetch('/api/terawih/reset')
                    .then(response => response.json())
                    .then(data => {
                        if(data.status === 'ok') loadData();
                    });
            }
        });
    }

    // Fungsi Bantuan Format
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    function formatTanggal(dateString) {
        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    }

    function getHari(dateString) {
        const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return hari[new Date(dateString).getDay()];
    }
});
