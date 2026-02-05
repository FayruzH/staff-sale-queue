<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Display - {{ $event->name }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root{
      --bg0:#0b1220;
      --muted: rgba(255,255,255,.72);
    }

    body{
      background:
        radial-gradient(1200px 600px at 20% 20%, rgba(59,130,246,.18), transparent 60%),
        radial-gradient(900px 500px at 90% 10%, rgba(168,85,247,.14), transparent 55%),
        var(--bg0);
      color:#fff;
    }

    .display-shell{ min-height:100vh; padding:24px; }

    .glass{
      background: rgba(18,26,43,.85);
      border:1px solid rgba(255,255,255,.08);
      backdrop-filter: blur(10px);
    }

    .big-title{ font-size:clamp(22px,2.6vw,40px); }
    .big-now{ font-size:clamp(44px,6.5vw,92px); font-weight:900; line-height:1; }
    .big-countdown{ font-size:clamp(40px,5.8vw,86px); font-weight:900; line-height:1; }
    .subtext{ color:var(--muted); font-size:clamp(14px,1.4vw,18px); }

    #nowServing, #nextBox{ border:3px solid transparent; }

    .pill{
      border:1px solid rgba(255,255,255,.10);
      background:rgba(255,255,255,.06);
      border-radius:999px;
      padding:6px 10px;
      font-size:12px;
      display:inline-flex;
      align-items:center;
      gap:8px;
      white-space: nowrap;
    }
  </style>
</head>

<body>
<div class="display-shell">
  <div class="container-fluid p-0">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
      <div>
        <div class="big-title fw-semibold">{{ $event->name }}</div>
        <div id="eventStatus" class="subtext">Loading…</div>
      </div>
      <div class="d-flex gap-2">
        <div class="pill"><i class="bi bi-broadcast"></i> Live</div>
        <div class="pill"><i class="bi bi-arrow-repeat"></i> Sync 5s</div>
      </div>
    </div>

    <div class="row g-3">
      {{-- NOW SERVING --}}
      <div class="col-12 col-lg-8">
        <div id="nowServing" class="glass rounded-4 p-4 h-100">
          <div class="d-flex justify-content-between align-items-center">
            <div class="subtext text-uppercase" style="letter-spacing:.18em;">Now Serving</div>
            <div class="pill"><i class="bi bi-clock-history"></i> Countdown</div>
          </div>

          <div class="mt-3">
            <div id="batchNumber" class="big-now">-</div>
            <div id="batchTime" class="subtext mt-2">-</div>

            {{-- idle info --}}
            <div id="idleInfo" class="subtext mt-2" style="display:none;">-</div>

            {{-- countdown to next batch --}}
            <div id="nextCountdown" class="big-countdown mt-2" style="display:none;">--:--</div>
          </div>

          <div class="mt-4 pt-2">
            <div class="subtext mb-2">Remaining</div>
            <div id="countdown" class="big-countdown">--:--</div>
          </div>
        </div>
      </div>

      {{-- NEXT --}}
      <div class="col-12 col-lg-4">
        <div id="nextBox" class="glass rounded-4 p-4 h-100">
          <div class="subtext text-uppercase" style="letter-spacing:.18em;">Next</div>
          <div class="mt-3">
            <div id="nextBatch" class="fw-bold" style="font-size:clamp(24px,2.8vw,40px);">-</div>
            <div id="nextTime" class="subtext mt-2">-</div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
const dataUrl = "{{ route('display.event.data', $event) }}";

const els = {
  status: document.getElementById('eventStatus'),
  batchNumber: document.getElementById('batchNumber'),
  batchTime: document.getElementById('batchTime'),
  countdown: document.getElementById('countdown'),
  nowBox: document.getElementById('nowServing'),
  nextBox: document.getElementById('nextBox'),
  nextBatch: document.getElementById('nextBatch'),
  nextTime: document.getElementById('nextTime'),
  idleInfo: document.getElementById('idleInfo'),
  nextCountdown: document.getElementById('nextCountdown'),
};

// monotonic clock smoothing
let baseServerNowMs = 0;
let basePerfNowMs = 0;

// server_now snapshot (for next-start countdown)
let serverNowMs = null;

// api states
let eventMeta=null, timezone=null;
let running=null, next=null;
let phase=null, breakWindow=null;

// render states
let lastShownSec=null, lastRunningId=null;

function pad(n){ return String(n).padStart(2,'0'); }
function formatMMSS(sec){
  sec=Math.max(0,Math.floor(sec));
  return `${pad(Math.floor(sec/60))}:${pad(sec%60)}`;
}

function estimatedServerNowMs(){
  if(!baseServerNowMs||!basePerfNowMs) return Date.now();
  return baseServerNowMs+(performance.now()-basePerfNowMs);
}

function applyBoxBorder(el,color){
  el.style.border = color ? `3px solid ${color}` : '3px solid transparent';
}

/**
 * Countdown to next batch start:
 */
function secondsUntilNextStart(){
  if(!next || !eventMeta || !serverNowMs) return null;


  // event_date: YYYY-MM-DD, start_time: HH:mm
  const startIso = `${eventMeta.event_date}T${next.start_time}:00`;
  const startMs = new Date(startIso).getTime();

  const diffSec = Math.floor((startMs - serverNowMs) / 1000);
  return Math.max(0, diffSec);
}

function renderStaticBits(){
  if(eventMeta){
    els.status.textContent =
      `Event: ${eventMeta.status} | Auto: ${eventMeta.auto_mode?'ON':'OFF'} | Phase: ${phase ?? '-'}`;
  }

  // Next box
  if(next){
    els.nextBatch.textContent = `Batch ${next.batch_number}`;
    els.nextTime.textContent  = `${next.start_time} - ${next.end_time}`;
    applyBoxBorder(els.nextBox, next.color_code || null);
  }else{
    els.nextBatch.textContent = '-';
    els.nextTime.textContent  = '-';
    applyBoxBorder(els.nextBox, null);
  }

  // Now serving
  if(running){
    els.idleInfo.style.display='none';
    els.nextCountdown.style.display='none';

    els.batchNumber.textContent = `Batch ${running.batch_number}`;
    els.batchTime.textContent   = `${running.start_time} - ${running.end_time}`;
    applyBoxBorder(els.nowBox, running.color_code || null);
  }else{
    els.batchNumber.textContent='-';
    els.countdown.textContent='--:--';
    els.idleInfo.style.display='block';

    if(phase==='waiting_start' && next){
      els.idleInfo.textContent = `Waiting — Batch ${next.batch_number} akan dimulai`;
      els.batchTime.textContent = `Next: ${next.start_time} - ${next.end_time}`;

      // show next-start countdown
      const sec = secondsUntilNextStart();
      if(sec !== null && sec <= 6 * 3600){
        els.nextCountdown.style.display='block';
      }else{
        els.nextCountdown.style.display='none';
      }

      applyBoxBorder(els.nowBox, next.color_code || null);
    }
    else if(phase==='break'){
      const bs = breakWindow?.break_start || '-';
      const be = breakWindow?.break_end || '-';
      els.idleInfo.textContent = `Break Time — ${bs} - ${be}`;
      els.batchTime.textContent = '-';
      els.nextCountdown.style.display='none';
      applyBoxBorder(els.nowBox,null);
    }
    else if(phase==='draft'){
      els.idleInfo.textContent='Event belum dimulai';
      els.batchTime.textContent='-';
      els.nextCountdown.style.display='none';
      applyBoxBorder(els.nowBox,null);
    }
    else if(phase==='ended'){
      els.idleInfo.textContent='Event telah selesai. Terima kasih.';
      els.batchTime.textContent='-';
      els.nextCountdown.style.display='none';
      applyBoxBorder(els.nowBox,null);
    }
    else{
      els.idleInfo.textContent='Menunggu batch berikutnya';
      els.batchTime.textContent='-';
      els.nextCountdown.style.display='none';
      applyBoxBorder(els.nowBox,null);
    }

    lastShownSec=null;
    lastRunningId=null;
  }
}

function tick(){
  // running batch countdown
  if(running && running.started_at && running.duration_seconds){
    const nowMs = estimatedServerNowMs();
    const startedAtMs = new Date(running.started_at).getTime();
    const remainSec = (startedAtMs + Number(running.duration_seconds)*1000 - nowMs) / 1000;

    const shown = Math.max(0, Math.floor(remainSec));
    if(shown!==lastShownSec || running.id!==lastRunningId){
      els.countdown.textContent = formatMMSS(shown);
      lastShownSec = shown;
      lastRunningId = running.id;
    }
  }

  // next-start countdown
  if(!running && phase==='waiting_start' && next && els.nextCountdown.style.display !== 'none'){
    const sec = secondsUntilNextStart();
    if(sec !== null){
      els.nextCountdown.textContent = formatMMSS(sec);
    }
  }

  requestAnimationFrame(tick);
}

async function syncFromServer(){
  try{
    const res = await fetch(dataUrl, { cache: 'no-store' });
    const json = await res.json();

    eventMeta    = json.event || null;
    timezone     = json.timezone || null;
    running      = json.running_batch || null;
    next         = json.next_batch || null;
    phase        = json.phase || null;
    breakWindow  = json.break_window || null;


    serverNowMs = json.server_now ? new Date(json.server_now).getTime() : Date.now();

    const newServerNowMs = serverNowMs;
    const nowPerf = performance.now();

    if(!baseServerNowMs){
      baseServerNowMs = newServerNowMs;
      basePerfNowMs = nowPerf;
    }else{
      const predicted = baseServerNowMs + (nowPerf - basePerfNowMs);
      baseServerNowMs = predicted + (newServerNowMs - predicted) * 0.2;
      basePerfNowMs = nowPerf;
    }

    renderStaticBits();
  }catch(e){
    console.error(e);
    els.status.textContent='Sync failed…';
  }
}

renderStaticBits();
tick();
syncFromServer();
setInterval(syncFromServer, 5000);
</script>

</body>
</html>
