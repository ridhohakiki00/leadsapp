@extends('layouts.app')

@section('content')
    <form id="report-form">


        <div class="row">
            <div class="col-3">

                <div class="mb-3 card p-4">
                    <h5 class="form-label">Field Data</h5>
                    <div id="field-options" class="form-check">
                        <!-- akan diisi otomatis -->
                    </div>
                </div>
            </div>
            <div class="col-9">

                <div class="mb-3 card p-4">
                    <h5 class="form-label">Kriteria Filter</h5>
                    <div id="filter-section">
                        <!-- akan diisi otomatis -->
                    </div>
                </div>
            </div>
        </div>


        <div class="mb-3">
            <label for="reportName" class="form-label">Nama Report</label>
            <input type="text" class="form-control" id="reportName" name="name" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Report</button>
    </form>

    <hr class="my-4">

    <div class="mt-4">
        <h5>Report Tersimpan</h5>
        <ul id="saved-reports" class="list-group"></ul>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const fieldOptions = document.getElementById('field-options');
            const filterSection = document.getElementById('filter-section');

            // Ambil semua field
            const schemaRes = await fetch('/report-schema');
            const schema = await schemaRes.json();

            // Tampilkan daftar field untuk checkbox tampilan
            schema.forEach(f => {
                const el = document.createElement('div');
                el.className = "form-check";
                el.innerHTML = `
            <input type="checkbox" class="form-check-input" name="fields[]" value="${f.name}" id="field-${f.name}">
            <label class="form-check-label" for="field-${f.name}">${f.name}</label>`;
                fieldOptions.appendChild(el);
            });

            // Tampilkan filter berdasarkan field
            for (let field of schema) {
                const row = document.createElement('div');
                row.className = "row mb-2 align-items-center";

                let html = `<div class="col-md-3">${field.name}</div>`;

                if (field.type === 'date') {
                    html += `
                <div class="col-md-4">
                    <input type="date" class="form-control filter-input" data-key="${field.name}" data-type="start">
                </div>
                <div class="col-md-4">
                    <input type="date" class="form-control filter-input" data-key="${field.name}" data-type="end">
                </div>`;
                } else {
                    const options = await fetch(`/report-options/${field.name}`).then(r => r.json());
                    html += `
                <div class="col-md-8">
                    <select multiple class="form-select filter-input" data-key="${field.name}">
                        ${options.map(v => `<option value="${v}">${v}</option>`).join('')}
                    </select>
                </div>`;
                }

                row.innerHTML = html;
                filterSection.appendChild(row);
            }

            // Inisialisasi Select2 setelah semua select selesai ditambahkan
            setTimeout(() => {
                document.querySelectorAll('select[multiple]').forEach(select => {
                    $(select).select2({
                        width: '100%',
                        placeholder: 'Pilih ' + select.dataset.key,
                        allowClear: true
                    });
                });
            }, 100);

            // Submit handler
            document.getElementById('report-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                const name = document.getElementById('reportName').value;
                const selectedFields = Array.from(document.querySelectorAll(
                    'input[name="fields[]"]:checked')).map(cb => cb.value);
                const criteria = {};

                if (selectedFields.length === 0) {
                    alert('Pilih minimal satu field untuk ditampilkan di report.');
                    return;
                }

                const inputs = document.querySelectorAll('.filter-input');
                let criteriaFilled = false; // penanda apakah ada filter yang diisi

                inputs.forEach(input => {
                    const key = input.dataset.key;
                    const type = input.dataset.type;

                    if (type === 'start' || type === 'end') {
                        if (input.value) {
                            criteriaFilled = true;
                            criteria[key] = criteria[key] || [];
                            if (type === 'start') criteria[key][0] = input.value;
                            if (type === 'end') criteria[key][1] = input.value;
                        }
                    } else if (input.tagName === 'SELECT' && input.multiple) {
                        const selected = Array.from(input.selectedOptions).map(o => o
                            .value);
                        if (selected.length > 0) {
                            criteriaFilled = true;
                            criteria[key] = selected;
                        }
                    }
                });

                if (!criteriaFilled) {
                    alert('Pilih minimal satu kriteria filter.');
                    return;
                }


                const response = await fetch('/report-create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: name,
                        fields: selectedFields,
                        criteria: criteria
                    })
                });

                if (response.ok) {
                    alert('Report berhasil disimpan!');
                    loadReports();
                } else {
                    alert('Gagal menyimpan report');
                }
            });

            // Load list report tersimpan
            async function loadReports() {
                const list = document.getElementById('saved-reports');
                list.innerHTML = '';
                const reports = await fetch('/api/reports').then(res => res.json());
                reports.forEach(r => {
                    const li = document.createElement('li');
                    li.className =
                        "list-group-item d-flex justify-content-between align-items-center";
                    li.innerHTML = `
                ${r.name}
                <span>
                    <a href="/report/${r.id}" class="btn btn-sm btn-info">Lihat</a>
                    <a href="/report-excel/${r.id}" class="btn btn-sm btn-success">Excel</a>
                    <a href="/report-pdf/${r.id}" class="btn btn-sm btn-danger">PDF</a>
                    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${r.id}">Hapus</button>
                </span>`;
                    list.appendChild(li);
                });
                // Event handler delete
                document.querySelectorAll('.btn-delete').forEach(btn => {
                    btn.addEventListener('click', async function() {
                        const id = this.dataset.id;
                        if (confirm('Yakin ingin menghapus report ini?')) {
                            const res = await fetch(`/report/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });

                            if (res.ok) {
                                alert('Report berhasil dihapus');
                                loadReports();
                            } else {
                                alert('Gagal menghapus report');
                            }
                        }
                    });
                });

            }

            loadReports();
        });
    </script>
@endsection
