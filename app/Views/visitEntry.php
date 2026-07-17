<?php include 'app/Views/templates/header.php'; ?>
<?php include 'app/Views/templates/topmenu.php'; ?>
<?php include 'app/Views/templates/sidemenu.php'; ?>
<?php
$currentUserName = session()->get('user')['name'] ?? 'Current user';
$searchTerm = $search_term ?? '';
$isLookupPerformed = $searchTerm !== '';
$isDriverFound = isset($driver) && $driver;
$pageTitle = 'Driver Check-In Desk';
$pageSubtitle = 'Verify registration and log driver visits at the highway counter.';
$pageEyebrow = 'Front Desk';
$breadcrumbs = [
    ['label' => 'Home', 'url' => base_url('dashboard')],
    ['label' => 'Driver Check-In', 'active' => true],
];
?>

<div class="content-wrapper ops-page-shell">
    <?php include 'app/Views/templates/page_header.php'; ?>

    <section class="content">
        <div class="container-fluid">
            <?php include 'app/Views/templates/flash_alerts.php'; ?>

            <!-- DRIVER LOOKUP -->
            <div class="card ops-card mb-4 visit-search-card">
                <div class="card-header ops-toolbar">
                    <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                        <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Driver Lookup</h3>
                        <span class="text-muted small" style="margin-top: 4px;">Handled by <?= esc($currentUserName) ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('visitEntry') ?>" method="post">
                        <?= csrf_field(); ?>
                        <div class="d-flex flex-column flex-sm-row">
                            <input type="text" class="form-control form-control-lg mb-2 mb-sm-0 mr-sm-2" name="search_term" id="search_term" value="<?= esc($searchTerm) ?>" placeholder="Mobile, vehicle, license, or ID" required style="font-size: 16px;">
                            <button type="submit" class="btn btn-primary-enterprise px-4" style="font-size: 15px; padding: 12px 24px;">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <!-- VERIFICATION RESULT -->
                <div class="col-lg-5 mb-4">
                    <div class="card ops-card h-100">
                        <div class="card-header ops-toolbar">
                            <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Verification Result</h3>
                            </div>
                            <?php if ($isLookupPerformed && $isDriverFound): ?>
                                <?php $driverStatus = (string) ($driver['status'] ?? 'active'); ?>
                                <span class="badge-enterprise-role" style="background: <?= in_array($driverStatus, ['blocked', 'blacklisted'], true) ? '#F43F5E' : '#10B981' ?>;">
                                    <i class="fas <?= in_array($driverStatus, ['blocked', 'blacklisted'], true) ? 'fa-exclamation-triangle' : 'fa-check-circle' ?> mr-1"></i>
                                    <?= esc(ucfirst($driverStatus)) ?>
                                </span>
                            <?php elseif ($isLookupPerformed): ?>
                                <span class="badge-enterprise-role" style="background: #4F4255;">
                                    <i class="fas fa-user-plus mr-1"></i> Not Registered
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if ($isDriverFound): ?>
                                <div class="snapshot-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                    <div class="snapshot-item">
                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;">Driver Name</div>
                                        <div style="font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600; color: #1A1C1C;"><?= esc($driver['driver_name']) ?></div>
                                    </div>
                                    <div class="snapshot-item">
                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;">Mobile</div>
                                        <div style="font-family: 'Inter', sans-serif; font-size: 14px; color: #1A1C1C;"><?= esc($driver['mobile_number']) ?></div>
                                    </div>
                                    <div class="snapshot-item">
                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;">License</div>
                                        <div style="font-family: 'Inter', sans-serif; font-size: 14px; color: #1A1C1C;"><?= esc($driver['license_number'] ?? 'Not captured') ?></div>
                                    </div>
                                    <div class="snapshot-item">
                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;">Latest Visit</div>
                                        <div style="font-family: 'Inter', sans-serif; font-size: 14px; color: #1A1C1C;"><?= !empty($driver['recent_visit_summary']['latest_visit_at']) ? esc($driver['recent_visit_summary']['latest_visit_at']) : 'No previous visit' ?></div>
                                    </div>
                                    <div class="snapshot-item">
                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #4F4255; text-transform: uppercase;">Today Visits</div>
                                        <div style="font-family: 'Inter', sans-serif; font-size: 14px; color: #1A1C1C;"><?= esc((string) ($driver['recent_visit_summary']['today_count'] ?? 0)) ?></div>
                                    </div>
                                </div>
                            <?php elseif ($isLookupPerformed): ?>
                                <div class="alert" style="background: #FFF1F2; border: 1px solid #FECDD3; color: #BE123C; border-radius: 4px; padding: 12px; font-family: 'Inter', sans-serif; font-size: 13px;">
                                    <strong>No driver found for "<?= esc($searchTerm) ?>".</strong><br>
                                    Register this driver first, then return here to log the visit.
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0" style="font-family: 'Inter', sans-serif; font-size: 13px;">Run a lookup to see the registration status and driver snapshot here.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- LOG VISIT FORM -->
                <div class="col-lg-7 mb-4">
                    <?php if ($isDriverFound): ?>
                        <div class="card ops-card">
                            <div class="card-header ops-toolbar">
                                <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                    <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Log Visit</h3>
                                    <span class="text-muted small" style="margin-top: 4px;">For Registered Driver #<?= esc((string) $driver['id']) ?></span>
                                </div>
                            </div>
                            <form action="<?= base_url('saveVisit') ?>" method="post" id="logVisitForm">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="driver_id" value="<?= esc((string) $driver['id']) ?>">
                                <input type="hidden" name="latitude" id="visit_latitude" value="">
                                <input type="hidden" name="longitude" id="visit_longitude" value="">
                                <input type="hidden" name="location_accuracy" id="visit_location_accuracy" value="">
                                <input type="hidden" name="location_address" id="visit_location_address" value="">
                                <div class="card-body">
                                    <div id="geoStatus" class="geo-status geo-status--pending">
                                        <i class="fas fa-location-arrow mr-1"></i>
                                        <span id="geoStatusText">Fetching current location…</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="guest_count">Guest Count</label>
                                                <input type="number" min="0" name="guest_count" id="guest_count" class="form-control" value="<?= esc(old('guest_count')) ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="vehicle_number">Vehicle Number</label>
                                                <input type="text" name="vehicle_number" id="vehicle_number" class="form-control" value="<?= esc(old('vehicle_number', $driver['vehicle_number'] ?? '')) ?>" maxlength="30" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="vehicle_type">Vehicle Type</label>
                                                <?php $selectedVehicleType = old('vehicle_type', $driver['vehicle_type'] ?? ''); ?>
                                                <select name="vehicle_type" id="vehicle_type" class="form-control" required>
                                                    <option value="">Select vehicle type</option>
                                                    <?php foreach (($vehicleTypeOptions ?? []) as $vehicleTypeOption): ?>
                                                        <option value="<?= esc($vehicleTypeOption) ?>" <?= $selectedVehicleType === $vehicleTypeOption ? 'selected' : '' ?>><?= esc(ucwords($vehicleTypeOption)) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cash_incentive_amount">Cash Incentive</label>
                                                <input type="number" step="0.01" min="0" name="cash_incentive_amount" id="cash_incentive_amount" class="form-control" value="<?= esc(old('cash_incentive_amount', number_format((float) ($driver['default_cash_incentive_amount'] ?? 200), 2, '.', ''))) ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="visited_at">Visit Time</label>
                                                <input type="datetime-local" name="visited_at" id="visited_at" class="form-control" value="<?= esc(old('visited_at', $visitDefaults['visited_at'] ?? '')) ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="food_offered">Food Issued</label>
                                                <select name="food_offered" id="food_offered" class="form-control" required>
                                                    <option value="" disabled selected>---Food Issued---</option>
                                                    <option value="1" <?= old('food_offered') == '1' ? 'selected' : '' ?>>Yes</option>
                                                    <option value="0" <?= old('food_offered') == '0' ? 'selected' : '' ?>>No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mt-2">
                                        <div class="remarks-label-row">
                                            <label for="remarks" class="mb-0">Remarks</label>
                                            <div class="dictation-controls" id="dictationControls" hidden>
                                                <div class="dictation-lang" role="group" aria-label="Dictation language">
                                                    <button type="button" class="dictation-lang-btn is-active" data-lang="hi-IN">हिंदी</button>
                                                    <button type="button" class="dictation-lang-btn" data-lang="en-IN">English</button>
                                                </div>
                                                <button type="button" id="dictateBtn" class="dictation-mic" aria-pressed="false" title="Bolein / Speak">
                                                    <i class="fas fa-microphone" aria-hidden="true"></i>
                                                    <span class="dictation-mic-text">Bolein</span>
                                                </button>
                                            </div>
                                        </div>
                                        <textarea name="remarks" id="remarks" class="form-control" rows="3" placeholder="Payment/Remarks"><?= esc(old('remarks')) ?></textarea>
                                        <small id="dictationStatus" class="dictation-status" role="status" aria-live="polite"></small>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-end align-items-center flex-wrap" style="gap:12px;">
                                        <a href="<?= site_url('visitEntryList') ?>" class="btn btn-outline-enterprise">Visit List</a>
                                        <button type="submit" class="btn btn-primary-enterprise">Save Visit Log</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php elseif ($isLookupPerformed): ?>
                        <div class="card ops-card">
                            <div class="card-header ops-toolbar">
                                <div style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                                    <h3 class="card-title mb-0" style="float: none; line-height: 1.2;">Driver Not Registered</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <p style="font-family: 'Inter', sans-serif; font-size: 13px; color: #4F4255;">
                                    No registered driver matched <strong><?= esc($searchTerm) ?></strong>. Use the driver registration form to create the driver record before logging visits.
                                </p>
                                <div style="display: flex; gap: 12px; margin-top: 16px;">
                                    <a href="<?= base_url('drivers/create') ?>" class="btn btn-primary-enterprise">Register Driver</a>
                                    <a href="<?= base_url('visitEntry') ?>" class="btn btn-outline-enterprise">Search Again</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card ops-card">
                            <div class="card-body text-center" style="padding: 40px 20px;">
                                <i class="fas fa-search mb-3" style="font-size: 24px; color: #E0E0E0;"></i>
                                <p class="text-muted mb-0" style="font-family: 'Inter', sans-serif; font-size: 14px;">Search for a driver to start a visit entry.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* =========================================
   ENTERPRISE LAYOUT & CARD
========================================= */
.ops-card {
    background: #FFFFFF;
    border-radius: 4px;
    border: 1px solid #E0E0E0;
    box-shadow: none;
    margin-bottom: 24px;
}
.ops-toolbar {
    background: #F5F5F5;
    padding: 16px 20px;
    border-bottom: 1px solid #E0E0E0;
    border-radius: 4px 4px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}
.ops-toolbar .card-title {
    font-family: 'Hanken Grotesk', sans-serif;
    font-weight: 600;
    color: #1A1C1C;
    font-size: 18px;
}
.ops-toolbar .text-muted {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    color: #4F4255;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-top: 4px;
}
.badge-enterprise-role {
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    font-weight: 500;
    padding: 4px 8px;
    background: #1A1C1C;
    color: #FFFFFF;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: inline-block;
}

/* =========================================
   FORM INPUTS
========================================= */
.form-group label {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    font-weight: 500;
    color: #4F4255;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
}
.form-control {
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    padding: 10px 12px;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    color: #1A1C1C;
    background: #FFFFFF;
    box-shadow: none !important;
    transition: all 0.2s ease;
    height: auto;
}
.form-control:focus {
    border-color: #A600FF;
    outline: 0;
}
textarea.form-control {
    min-height: 80px;
}

/* =========================================
   BUTTONS
========================================= */
.btn-primary-enterprise {
    background: #A600FF;
    color: #FFFFFF;
    border: none;
    border-radius: 4px;
    font-family: 'Hanken Grotesk', sans-serif;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 20px;
    transition: background 0.2s;
    text-decoration: none;
}
.btn-primary-enterprise:hover {
    background: #8300CA;
    color: #FFFFFF;
}
.btn-outline-enterprise {
    background: transparent;
    color: #1A1C1C;
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    font-family: 'Hanken Grotesk', sans-serif;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 20px;
    transition: all 0.2s;
    text-decoration: none;
}
.btn-outline-enterprise:hover {
    background: #F5F5F5;
    border-color: #1A1C1C;
}

.card-footer {
    background: #FFFFFF;
    padding: 20px;
    border-top: 1px solid #E0E0E0 !important;
    border-radius: 0 0 4px 4px;
}

/* =========================================
   GEOLOCATION STATUS
========================================= */
.geo-status {
    display: flex;
    align-items: center;
    font-family: 'Inter', sans-serif;
    font-size: 12px;
    font-weight: 500;
    padding: 8px 12px;
    border-radius: 4px;
    margin-bottom: 16px;
    border: 1px solid transparent;
}
.geo-status--pending {
    background: #FFF7ED;
    border-color: #FED7AA;
    color: #C2410C;
}
.geo-status--success {
    background: #ECFDF5;
    border-color: #A7F3D0;
    color: #047857;
}
.geo-status--error {
    background: #FFF1F2;
    border-color: #FECDD3;
    color: #BE123C;
}

/* =========================================
   SPEECH-TO-TEXT (DICTATION)
========================================= */
.remarks-label-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 6px;
}
.dictation-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}
.dictation-lang {
    display: inline-flex;
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    overflow: hidden;
}
.dictation-lang-btn {
    background: #FFFFFF;
    border: none;
    padding: 5px 10px;
    font-family: 'Inter', sans-serif;
    font-size: 12px;
    font-weight: 500;
    color: #4F4255;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}
.dictation-lang-btn + .dictation-lang-btn {
    border-left: 1px solid #E0E0E0;
}
.dictation-lang-btn.is-active {
    background: #1A1C1C;
    color: #FFFFFF;
}
.dictation-mic {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #FFFFFF;
    border: 1px solid #A600FF;
    color: #A600FF;
    border-radius: 4px;
    padding: 5px 12px;
    font-family: 'Hanken Grotesk', sans-serif;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s, color 0.15s, box-shadow 0.15s;
}
.dictation-mic:hover {
    background: #F7ECFF;
}
.dictation-mic.is-listening {
    background: #A600FF;
    color: #FFFFFF;
    box-shadow: 0 0 0 0 rgba(166, 0, 255, 0.5);
    animation: dictation-pulse 1.4s infinite;
}
@keyframes dictation-pulse {
    0%   { box-shadow: 0 0 0 0 rgba(166, 0, 255, 0.45); }
    70%  { box-shadow: 0 0 0 8px rgba(166, 0, 255, 0); }
    100% { box-shadow: 0 0 0 0 rgba(166, 0, 255, 0); }
}
.dictation-status {
    display: none;
    margin-top: 6px;
    font-family: 'Inter', sans-serif;
    font-size: 12px;
    color: #4F4255;
}
.dictation-status.is-visible {
    display: block;
}
.dictation-status.is-error {
    color: #BE123C;
}
</style>

<script>
(function () {
    var form = document.getElementById('logVisitForm');
    if (!form) {
        return;
    }

    var latField = document.getElementById('visit_latitude');
    var lngField = document.getElementById('visit_longitude');
    var accField = document.getElementById('visit_location_accuracy');
    var addrField = document.getElementById('visit_location_address');
    var statusBox = document.getElementById('geoStatus');
    var statusText = document.getElementById('geoStatusText');

    function setStatus(state, message) {
        if (!statusBox) {
            return;
        }
        statusBox.className = 'geo-status geo-status--' + state;
        if (statusText) {
            statusText.textContent = message;
        }
    }

    // Best-effort browser-side reverse geocoding for instant feedback.
    // If it fails, the server reverse-geocodes on save, so this never blocks.
    function resolveAddress(lat, lng, acc) {
        var url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&zoom=18&addressdetails=1'
            + '&lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng);

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (response) { return response.ok ? response.json() : null; })
            .then(function (data) {
                var address = data && data.display_name ? String(data.display_name) : '';
                if (address) {
                    addrField.value = address.substring(0, 255);
                    setStatus('success', 'Location captured (±' + Math.round(acc) + ' m): ' + address);
                }
            })
            .catch(function () { /* keep coordinate-only status */ });
    }

    if (!('geolocation' in navigator)) {
        setStatus('error', 'Location not supported on this device. Visit will be saved without it.');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function (position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            var acc = position.coords.accuracy;

            latField.value = lat.toFixed(7);
            lngField.value = lng.toFixed(7);
            accField.value = (acc !== null && acc !== undefined) ? Math.round(acc) : '';

            setStatus('success', 'Location captured (±' + Math.round(acc) + ' m): '
                + lat.toFixed(5) + ', ' + lng.toFixed(5));

            resolveAddress(lat.toFixed(7), lng.toFixed(7), acc);
        },
        function (error) {
            var reason = 'Location unavailable';
            if (error && error.code === error.PERMISSION_DENIED) {
                reason = 'Location permission denied';
            } else if (error && error.code === error.TIMEOUT) {
                reason = 'Location request timed out';
            }
            setStatus('error', reason + '. Visit will be saved without it.');
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
})();
</script>

<script>
// =========================================
// SPEECH-TO-TEXT for the Remarks field.
// Uses the browser-native Web Speech API (free, no key). Supported on
// Chrome / Edge (incl. Android). Silently hidden on unsupported browsers,
// so staff can always type manually.
// =========================================
(function () {
    var textarea = document.getElementById('remarks');
    var controls = document.getElementById('dictationControls');
    var micBtn = document.getElementById('dictateBtn');
    var statusEl = document.getElementById('dictationStatus');
    var langButtons = document.querySelectorAll('.dictation-lang-btn');

    if (!textarea || !controls || !micBtn) {
        return;
    }

    var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
        // Browser can't do speech recognition — leave manual typing as-is.
        return;
    }

    controls.hidden = false;

    var recognition = new SpeechRecognition();
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.lang = 'hi-IN';

    var listening = false;
    // Text already committed to the textarea when the current session started.
    var baseText = '';
    // Sum of final results received during the current session.
    var sessionFinal = '';

    function setStatus(message, isError) {
        if (!statusEl) {
            return;
        }
        statusEl.textContent = message || '';
        statusEl.classList.toggle('is-visible', !!message);
        statusEl.classList.toggle('is-error', !!isError);
    }

    function joinText(a, b) {
        if (!a) { return b; }
        if (!b) { return a; }
        return /\s$/.test(a) ? (a + b) : (a + ' ' + b);
    }

    function startListening() {
        baseText = textarea.value;
        sessionFinal = '';
        try {
            recognition.start();
        } catch (e) {
            // start() throws if called while already starting; ignore.
        }
    }

    function stopListening() {
        try {
            recognition.stop();
        } catch (e) { /* no-op */ }
    }

    micBtn.addEventListener('click', function () {
        if (listening) {
            stopListening();
        } else {
            startListening();
        }
    });

    langButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            langButtons.forEach(function (b) { b.classList.remove('is-active'); });
            btn.classList.add('is-active');
            recognition.lang = btn.getAttribute('data-lang');
            if (listening) {
                // Restart so the new language takes effect immediately.
                stopListening();
            }
        });
    });

    recognition.addEventListener('start', function () {
        listening = true;
        micBtn.classList.add('is-listening');
        micBtn.setAttribute('aria-pressed', 'true');
        setStatus('Sun raha hoon… boliye (Listening…)', false);
    });

    recognition.addEventListener('result', function (event) {
        var interim = '';
        for (var i = event.resultIndex; i < event.results.length; i++) {
            var transcript = event.results[i][0].transcript;
            if (event.results[i].isFinal) {
                sessionFinal = joinText(sessionFinal, transcript.trim());
            } else {
                interim += transcript;
            }
        }
        var combined = joinText(baseText, sessionFinal);
        combined = joinText(combined, interim.trim());
        textarea.value = combined;
    });

    recognition.addEventListener('error', function (event) {
        var message = 'Voice input error. Please type manually.';
        if (event.error === 'not-allowed' || event.error === 'service-not-allowed') {
            message = 'Microphone permission denied. Allow mic access to dictate.';
        } else if (event.error === 'no-speech') {
            message = 'No speech detected. Tap the mic and try again.';
        } else if (event.error === 'network') {
            message = 'Network needed for voice input. Check your connection.';
        } else if (event.error === 'aborted') {
            message = '';
        }
        setStatus(message, !!message);
    });

    recognition.addEventListener('end', function () {
        listening = false;
        micBtn.classList.remove('is-listening');
        micBtn.setAttribute('aria-pressed', 'false');
        // Persist whatever was captured as the new base for the next session.
        baseText = textarea.value;
        sessionFinal = '';
        if (statusEl && !statusEl.classList.contains('is-error')) {
            setStatus('', false);
        }
    });

    // Stop capturing before the form submits so nothing is lost mid-word.
    var form = document.getElementById('logVisitForm');
    if (form) {
        form.addEventListener('submit', function () {
            if (listening) {
                stopListening();
            }
        });
    }
})();
</script>

<?php include 'app/Views/templates/footer.php'; ?>
