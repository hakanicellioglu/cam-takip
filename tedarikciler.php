<?php
require_once 'header.php';
?>
<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 mb-0">Tedarikçiler</h1>
    <div>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#supplierModal">Yeni Tedarikçi</button>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <form id="filterForm" class="row g-2 mb-3">
        <div class="col-md-4"><input type="text" class="form-control" name="q" placeholder="Firma adı / vergi no / e-posta"></div>
        <div class="col-md-3">
          <select class="form-select" name="is_active">
            <option value="">Tümü (Aktif+Pasif)</option>
            <option value="1" selected>Aktif</option>
            <option value="0">Pasif</option>
          </select>
        </div>
        <div class="col-md-2"><button class="btn btn-outline-primary w-100" id="btnSearch">Ara</button></div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover align-middle" id="tblSuppliers">
          <thead>
            <tr><th>Firma Adı</th><th>Adres</th><th>E-posta</th><th>Vergi No</th><th>Durum</th><th>Kişiler</th><th class="text-end">İşlemler</th></tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <nav><ul class="pagination justify-content-end" id="pager"></ul></nav>
    </div>
  </div>
</div>

<!-- Firma Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="frmSupplier">
      <div class="modal-header"><h5 class="modal-title">Tedarikçi</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="id"><input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <div class="mb-2"><label class="form-label">Firma Adı *</label><input class="form-control" name="name" required></div>
        <div class="mb-2"><label class="form-label">Adres</label><input class="form-control" name="address"></div>
        <div class="mb-2"><label class="form-label">E-posta</label><input class="form-control" name="email" type="email"></div>
        <div class="mb-2"><label class="form-label">Vergi No</label><input class="form-control" name="tax_no"></div>
        <div class="mb-2"><label class="form-label">Notlar</label><textarea class="form-control" name="notes" rows="3"></textarea></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="chkActive"><label class="form-check-label" for="chkActive">Aktif</label></div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Kapat</button><button class="btn btn-primary" id="btnSaveSupplier">Kaydet</button></div>
    </form>
  </div>
</div>

<!-- Kişiler Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="contactsPanel">
  <div class="offcanvas-header"><h5 class="offcanvas-title">Firma Kişileri</h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div>
  <div class="offcanvas-body">
    <div class="d-flex justify-content-end mb-2"><button class="btn btn-primary" id="btnNewContact">Yeni Kişi</button></div>
    <table class="table table-sm" id="tblContacts"><thead><tr><th>Ad Soyad</th><th>Rol</th><th>Telefon</th><th>E-posta</th><th>Birincil</th><th></th></tr></thead><tbody></tbody></table>
  </div>
</div>

<!-- Kişi Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="frmContact">
      <div class="modal-header"><h5 class="modal-title">Kişi</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="id"><input type="hidden" name="supplier_id"><input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <div class="mb-2"><label class="form-label">Ad Soyad *</label><input class="form-control" name="full_name" required></div>
        <div class="mb-2"><label class="form-label">Rol</label><input class="form-control" name="role"></div>
        <div class="mb-2"><label class="form-label">Telefon</label><input class="form-control" name="phone"></div>
        <div class="mb-2"><label class="form-label">E-posta</label><input class="form-control" name="email" type="email"></div>
        <div class="mb-2"><label class="form-label">Notlar</label><input class="form-control" name="notes"></div>
        <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="is_primary" value="1" id="chkPrimary"><label class="form-check-label" for="chkPrimary">Birincil</label></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="chkContactActive"><label class="form-check-label" for="chkContactActive">Aktif</label></div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Kapat</button><button class="btn btn-primary" id="btnSaveContact">Kaydet</button></div>
    </form>
  </div>
</div>

</div>
<!-- main-content close -->

<script>
const tblSuppliers=document.querySelector('#tblSuppliers tbody');
const filterForm=document.getElementById('filterForm');
const supplierModal=new bootstrap.Modal(document.getElementById('supplierModal'));
const contactPanel=new bootstrap.Offcanvas(document.getElementById('contactsPanel'));
const contactModal=new bootstrap.Modal(document.getElementById('contactModal'));
let currentSupplierId=0;

function loadSuppliers(page=1){
  const fd=new FormData(filterForm);fd.append('page',page);
  fetch('api/suppliers/list.php?'+new URLSearchParams(fd).toString())
    .then(r=>r.json()).then(res=>{
      tblSuppliers.innerHTML='';
      res.data.rows.forEach(r=>{
        const tr=document.createElement('tr');
        tr.innerHTML=`<td>${r.name}</td><td>${r.address||''}</td><td>${r.email||''}</td><td>${r.tax_no||''}</td>`+
          `<td><span class="badge ${r.is_active==1?'bg-success':'bg-secondary'}">${r.is_active==1?'Aktif':'Pasif'}</span></td>`+
          `<td><span class=\"badge bg-info text-dark\">${r.contact_count}</span></td>`+
          `<td class='text-end'><button class='btn btn-sm btn-outline-secondary me-1' onclick='editSupplier(${r.id})'>Düzenle</button>`+
          `<button class='btn btn-sm btn-outline-primary me-1' onclick='showContacts(${r.id})'>Kişiler</button>`+
          `<button class='btn btn-sm btn-outline-danger' onclick='deleteSupplier(${r.id})'>Sil</button></td>`;
        tblSuppliers.appendChild(tr);
      });
    });
}

filterForm.addEventListener('submit',e=>{e.preventDefault();loadSuppliers();});
loadSuppliers();

function editSupplier(id){
  fetch('api/suppliers/list.php?id='+id).then(r=>r.json()).then(res=>{
    const s=res.data.rows.find(x=>x.id==id);if(!s)return;
    const f=document.getElementById('frmSupplier');
    for(const k in s){if(f[k])f[k].value=s[k];}
    f.is_active.checked=s.is_active==1;
    supplierModal.show();
  });
}

function deleteSupplier(id){
  if(!confirm('Silinsin mi?'))return;
  fetch('api/suppliers/delete.php',{method:'POST',body:new URLSearchParams({id:id,csrf:'<?= $_SESSION['csrf_token'] ?>'})})
    .then(r=>r.json()).then(()=>loadSuppliers());
}

function showContacts(supplierId){
  currentSupplierId=supplierId;
  document.querySelector('#frmContact [name=supplier_id]').value=supplierId;
  loadContacts();
  contactPanel.show();
}

function loadContacts(){
  fetch('api/contacts/list.php?supplier_id='+currentSupplierId)
    .then(r=>r.json()).then(res=>{
      const tbody=document.querySelector('#tblContacts tbody');
      tbody.innerHTML='';
      res.data.rows.forEach(r=>{
        const tr=document.createElement('tr');
        tr.innerHTML=`<td>${r.full_name}</td><td>${r.role||''}</td><td>${r.phone||''}</td><td>${r.email||''}</td>`+
          `<td>${r.is_primary==1?'Evet':'Hayır'}</td>`+
          `<td class='text-end'><button class='btn btn-sm btn-outline-secondary me-1' onclick='editContact(${r.id})'>Düzenle</button>`+
          `<button class='btn btn-sm btn-outline-danger' onclick='deleteContact(${r.id})'>Sil</button></td>`;
        tbody.appendChild(tr);
      });
    });
}

document.getElementById('btnNewContact').addEventListener('click',()=>{
  document.getElementById('frmContact').reset();
  document.querySelector('#frmContact [name=id]').value='';
  contactModal.show();
});

document.getElementById('frmSupplier').addEventListener('submit',e=>{
  e.preventDefault();
  const fd=new FormData(e.target);
  fetch('api/suppliers/save.php',{method:'POST',body:fd})
    .then(r=>r.json()).then(res=>{if(res.ok){supplierModal.hide();loadSuppliers();}});
});

document.getElementById('frmContact').addEventListener('submit',e=>{
  e.preventDefault();
  const fd=new FormData(e.target);
  fetch('api/contacts/save.php',{method:'POST',body:fd})
    .then(r=>r.json()).then(res=>{if(res.ok){contactModal.hide();loadContacts();loadSuppliers();}});
});

function editContact(id){
  fetch('api/contacts/list.php?supplier_id='+currentSupplierId)
    .then(r=>r.json()).then(res=>{
      const c=res.data.rows.find(x=>x.id==id);if(!c)return;
      const f=document.getElementById('frmContact');
      for(const k in c){if(f[k])f[k].value=c[k];}
      f.is_primary.checked=c.is_primary==1;f.is_active.checked=c.is_active==1;
      contactModal.show();
    });
}

function deleteContact(id){
  if(!confirm('Silinsin mi?'))return;
  fetch('api/contacts/delete.php',{method:'POST',body:new URLSearchParams({id:id,csrf:'<?= $_SESSION['csrf_token'] ?>'})})
    .then(r=>r.json()).then(()=>{loadContacts();loadSuppliers();});
}
</script>

<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>
