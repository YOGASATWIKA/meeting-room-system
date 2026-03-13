<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Create New Booking</h2>
    <a href="<?= BASEURL ?>/rooms/index" class="btn">← Browse Rooms</a>
</div>

<div class="card">
    <form method="POST" action="<?= BASEURL ?>/bookings/store">
        <div class="form-group">
            <label for="room_id">Select Room *</label>
            <select id="room_id" name="room_id" required>
                <option value="">-- Select a Room --</option>
                <?php 
                $selectedRoomId = $_GET['room_id'] ?? '';
                foreach($rooms as $room): 
                ?>
                    <option value="<?= $room['id'] ?>" <?= $selectedRoomId == $room['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($room['name']) ?> - Capacity: <?= $room['capacity'] ?> - Rp <?= number_format($room['price_per_hour'], 0, ',', '.') ?>/hour
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="booking_date">Booking Date *</label>
            <input type="date" id="booking_date" name="booking_date" min="<?= date('Y-m-d') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="start_time">Start Time *</label>
            <input type="time" id="start_time" name="start_time" required>
        </div>
        
        <div class="form-group">
            <label for="end_time">End Time *</label>
            <input type="time" id="end_time" name="end_time" required>
        </div>
        
        <div class="form-group">
            <label for="purpose">Purpose/Notes</label>
            <textarea id="purpose" name="purpose" rows="3" placeholder="Meeting purpose or additional notes..."></textarea>
        </div>
        
        <div id="price_estimate" style="background: #e3f2fd; padding: 15px; border-radius: 5px; margin-bottom: 15px; display: none;">
            <strong>Estimated Price:</strong> <span id="estimated_price">Rp 0</span>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn" style="width: 100%;">Create Booking</button>
        </div>
    </form>
</div>

<div class="card">
    <h3>Booking Guidelines</h3>
    <ul style="list-style: disc; padding-left: 30px;">
        <li>Bookings must be made at least 1 day in advance</li>
        <li>Minimum booking duration is 1 hour</li>
        <li>Check room availability before booking</li>
        <li>Cancellation must be made at least 24 hours before the booking time</li>
        <li>Price is calculated per hour based on the room rate</li>
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
        alert('End time must be after start time');
        return;
    }
    
    const totalPrice = hours * pricePerHour;
    estimatedPriceSpan.textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
    priceEstimateDiv.style.display = 'block';
}

roomSelect.addEventListener('change', calculatePrice);
startTimeInput.addEventListener('change', calculatePrice);
endTimeInput.addEventListener('change', calculatePrice);
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
