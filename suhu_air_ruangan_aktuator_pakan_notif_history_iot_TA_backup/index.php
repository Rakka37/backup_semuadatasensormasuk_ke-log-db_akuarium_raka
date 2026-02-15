<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Monitoring Akuarium IoT</title>

<style>
:root{
  --bg:#020617;
  --card:#0f172a;
  --accent:#38bdf8;
  --text:#e5e7eb;
  --soft:#94a3b8;
}
*{box-sizing:border-box}
body{
  margin:0;min-height:100vh;background:var(--bg);color:var(--text);
  font-family:system-ui;display:flex;flex-direction:column;align-items:center;
}
header{max-width:900px;text-align:center;padding:20px 15px 10px}
header h1{font-size:clamp(1.1rem,3vw,1.8rem)}
header p{color:var(--soft)}
.menu{display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin:10px 0}
.menu button{
  padding:8px 14px;border-radius:10px;border:1px solid var(--accent);
  background:#020617;color:var(--text);cursor:pointer
}
.menu button.active{background:var(--accent);color:#020617;font-weight:700}
.container{
  width:100%;max-width:900px;padding:15px;
  display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px
}
.card{
  background:linear-gradient(145deg,#0f172a,#020617);
  border-radius:18px;padding:22px;box-shadow:0 15px 30px rgba(0,0,0,.4)
}
.card-title{color:var(--soft);margin-bottom:6px}
.value{font-size:2.2rem;font-weight:700;color:var(--accent)}
button.main{
  width:100%;padding:12px;border:none;border-radius:12px;
  background:var(--accent);color:#020617;font-weight:700;cursor:pointer
}
#notif{
  position:fixed;top:20px;left:50%;transform:translateX(-50%);
  min-width:280px;padding:16px 22px;border-radius:16px;
  background:#020617;border:2px solid var(--accent);
  display:none;z-index:999;text-align:center
}
.section{width:100%;max-width:900px;padding:15px;display:none}
.section.active{display:block}
table{width:100%;border-collapse:collapse;font-size:.85rem}
th,td{padding:8px;border-bottom:1px solid #1e293b}
th{color:var(--soft);text-align:left}
.images{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px
}
.images img{width:100%;border-radius:12px;border:2px solid #1e293b}
footer{margin:15px 0;font-size:.75rem;color:#64748b}
</style>
</head>

<body>

<header>
  <h1>Monitoring Akuarium IoT</h1>
  <p>Monitoring & Kontrol Real-Time</p>
</header>

<div class="menu">
  <button class="active" onclick="openTab('monitor',this)">Monitoring</button>
  <button onclick="openTab('sensor',this)">Sensor</button>
  <button onclick="openTab('aktuator',this)">Aktuator</button>
  <button onclick="openTab('pakan',this)">Pakan</button>
  <button onclick="openTab('notiflog',this)">Notifikasi</button>
  <button onclick="openTab('gambar',this)">Gambar</button>
</div>

<div id="monitor" class="section active">
  <div class="container">
    <div class="card"><div class="card-title">🌡️ Suhu Ruangan</div><div class="value"><span id="ruangan">--</span> °C</div></div>
    <div class="card"><div class="card-title">💧 Suhu Air</div><div class="value"><span id="air">--</span> °C</div></div>
    <div class="card"><div class="card-title">🧪 Kekeruhan</div><div class="value"><span id="ntu">--</span> NTU</div></div>
    <div class="card"><div class="card-title">🍽️ Pakan</div><button class="main" onclick="feed()">Beri Makan Manual</button></div>
  </div>
</div>

<div id="sensor" class="section"><table id="tblSensor"></table></div>
<div id="aktuator" class="section"><table id="tblAktuator"></table></div>
<div id="pakan" class="section"><table id="tblPakan"></table></div>
<div id="notiflog" class="section"><table id="tblNotif"></table></div>
<div id="gambar" class="section"><div class="images" id="imgList"></div></div>

<footer>© Sistem Akuarium IoT</footer>
<div id="notif"></div>

<script>
// TAB
function openTab(id,btn){
  document.querySelectorAll('.section').forEach(s=>s.classList.remove('active'));
  document.querySelectorAll('.menu button').forEach(b=>b.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  btn.classList.add('active');
}

// SENSOR
function loadData(){
  fetch("get.php",{cache:"no-store"}).then(r=>r.json()).then(d=>{
    ruangan.innerText=d.ruangan;
    air.innerText=d.air;
    ntu.innerText=d.ntu;
  });
}
setInterval(loadData,2000);

// MANUAL FEED (NO NOTIF HERE)
function feed(){
  fetch("feed.php?feed=1",{cache:"no-store"});
}

// FLOATING NOTIF (UI ONLY)
let queue=[],active=false;
function pushNotif(t){
  queue.push(t);
  if(!active) runNotif();
}
function runNotif(){
  if(queue.length===0){active=false;hideNotif();return;}
  active=true;
  notif.innerText=queue.shift();
  notif.style.display="block";
  setTimeout(()=>{notif.style.display="none";setTimeout(runNotif,500)},1500);
}

// REALTIME NOTIF FROM DB (SINGLE SOURCE)
let lastNotifId=null;
function cekNotifRealtime(){
  fetch("last_notif.php",{cache:"no-store"}).then(r=>r.json()).then(d=>{
    if(!d) return;
    if(lastNotifId===null){lastNotifId=d.id;return;}
    if(d.id>lastNotifId){
      lastNotifId=d.id;
      pushNotif(d.pesan);
      loadAllHistory();
    }
  });
}
setInterval(cekNotifRealtime,1000);

// HISTORY
function loadTable(url,el,head){
  fetch(url,{cache:"no-store"}).then(r=>r.json()).then(d=>{
    let h="<tr>"+head.map(x=>"<th>"+x+"</th>").join("")+"</tr>";
    d.forEach(r=>h+="<tr>"+Object.values(r).map(v=>"<td>"+v+"</td>").join("")+"</tr>");
    el.innerHTML=h;
  });
}
function loadAllHistory(){
  loadTable("history_sensor.php",tblSensor,["Waktu","Ruangan","Air","NTU","Kualitas"]);
  loadTable("history_aktuator.php",tblAktuator,["Waktu","Aktuator","Aksi","Sebab"]);
  loadTable("history_pakan.php",tblPakan,["Waktu","Mode","Putaran","Keterangan"]);
  loadTable("history_notifikasi.php",tblNotif,["Waktu","Jenis","Pesan"]);
}
setInterval(loadAllHistory,3000);
loadAllHistory();

// IMAGES
fetch("get_images.php").then(r=>r.json()).then(d=>{
  imgList.innerHTML="";
  d.forEach(i=>imgList.innerHTML+=`<img src="${i.path}">`);
});
</script>

</body>
</html>
