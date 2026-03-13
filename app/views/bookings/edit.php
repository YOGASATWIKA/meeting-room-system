<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Edit Booking</h2>
    <a href="<?= BASEURL ?>/bookings/show/<?= $booking['id'] ?>" class="btn">← Back to Booking</a>
</div>

<div class="card">
    <form method="POST" action="<?= BASEURL ?>/bookings/update/<?= $booking['id'] ?>">
        <div class="form-group">
            <label for="room_id">Select Room *</label>
            <select id="room_id" name="room_id" required>
                <option value="">-- Select a Room --</option>
                <?php foreach($rooms as $room): ?>
                    <option value="<?= $room['id'] ?>" <?= $booking['room_id'] == $room['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($room['name']) ?> - Capacity: <?= $room['capacity'] ?> - Rp <?= number_format($room['price_per_hour'], 0, ',', '.') ?>/hour
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="booking_date">Booking Date *</label>
            <input type="date" id="booking_date" name="booking_date" value="<?= htmlspecialchars($booking['booking_date']) ?>" min="<?= date('Y-m-d') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="start_time">Start Time *</label>
            <input type="time" id="start_time" name="start_time" value="<?= htmlspecialchars($booking['start_time']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="end_time">End Time *</label>
            <input type="time" id="end_time" name="end_time" value="<?= htmlspecialchars($booking['end_time']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="purpose">Purpose/Notes</label>
            <textarea id="purpose" name="purpose" rows="3" placeholder="Meeting purpose or additional notes..."><?= htmlspecialchars($booking['purpose'] ?? '') ?></textarea>
        </div>
        
        <div id="price_estimate" style="background: #e3f2fd; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
            <strong>Estimated Price:</strong> <span id="estimated_price">Rp 0</span>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn" style="width: 100%;">Update Booking</button>
            <a href="<?= BASEURL ?>/bookings/show/<?= $booking['id'] ?>" class="btn btn-danger" style="width: 100%; margin-top: 10px;">Cancel</a>
        </div>
    </form>
</div>

<div class="card">
    <h3>Booking Guidelines</h3>
    <ul style="list-style: disc; padding-left: 30px;">
        <li>Only pending bookings can be edited</li>
        <li>Booking date cannot be in the past</li>
        <li>Check room availability before updating</li>
        <li>Price will be recalculated based on new duration and room rate</li>
    </ul>
</div>

<script>
// Calculate price estimate
const roomSelect = document.getElementById('room_id');
const startTimeInput = document.getElementById('start_time');
const endTimeInput = document.getElementById('end_time');
const priceEstimateDiv = document.getElementById('price_estimate');
const estimatedPriceSpan = document.getElementById('estimated_price');

function calculatePrice() {
    const selectedOption = roomSelect.options[roomSelect.selectedIndex];
    if (!selectedOption.value || !startTimeInput.value || !endTimeInput.value) {
        priceEstimateDiv.style.display = 'none';
        return;
    }
    
    // Extract price from option text (format: "... - Rp XXX,XXX/hour")
    const optionText = selectedOption.text;
    const priceMatch = optionText.match(/Rp ([\d,]+)\/hour/);
    if (!priceMatch) return;
    
    const pricePerHour = parseInt(priceMatch[1].replace(/,/g, ''));
    
    // Calculate duration
    const startTime = new Date('2000-01-01 ' + startTimeInput.value);
    const endTime = new Date('2000-01-01 ' + endTimeInput.value);
    const hours = (endTime - startTime) / (1000 * 60 * 60);
    
    if (hours <= 0) {
        return;
    }
    
    const totalPrice = hours * pricePerHour;
    estimatedPriceSpan.textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
    priceEstimateDiv.style.display = 'block';
}

roomSelect.addEventListener('change', calculatePrice);
startTimeInput.addEventListener('change', calculatePrice);
endTimeInput.addEventListener('change', calculatePrice);

// Calculate initial price
calculatePrice();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
