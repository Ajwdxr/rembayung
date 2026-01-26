<?php

/**
 * Rembayung - Booking Page
 */
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Reserve a Table';
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<!-- Hero -->
<section class="pt-32 pb-16 bg-kampung-charcoal text-white relative overflow-hidden">
    <div class="absolute inset-0 batik-pattern opacity-10"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <p class="text-kampung-gold font-medium tracking-wider uppercase mb-4 animate-fadeInUp">Reservations</p>
        <h1 class="font-heading text-5xl md:text-6xl font-bold mb-6 animate-fadeInUp animate-delay-100">
            Reserve Your Table
        </h1>
        <p class="text-white/80 text-lg max-w-2xl mx-auto animate-fadeInUp animate-delay-200">
            Book your dining experience at Rembayung. We recommend making reservations
            at least 24 hours in advance for the best availability.
        </p>
    </div>
</section>

<!-- Booking Form Section -->
<section class="py-20 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Form -->
        <div id="bookingFormContainer" class="bg-kampung-cream rounded-3xl p-8 md:p-12 shadow-xl scroll-reveal">
            <form id="bookingForm" action="<?= BASE_URL ?>/api/booking_submit.php" method="POST">
                <div class="space-y-6">

                    <!-- Calendar Section -->
                    <div class="mb-6">
                        <label class="form-label mb-3">
                            Select Date <span class="text-red-500">*</span>
                        </label>

                        <!-- Calendar Legend -->
                        <div class="flex flex-wrap gap-4 mb-4 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                <span class="text-gray-600">Available</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                                <span class="text-gray-600">Limited</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                                <span class="text-gray-600">Full</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                                <span class="text-gray-600">Closed</span>
                            </div>
                        </div>

                        <!-- Calendar Component -->
                        <div class="bg-kampung-charcoal text-white rounded-xl overflow-hidden calendar-grid-container">
                            <!-- Calendar Header -->
                            <div class="flex items-center justify-between p-4 bg-kampung-charcoal border-b border-white/10">
                                <button type="button" id="prevMonth" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <h3 id="calendarTitle" class="font-semibold text-lg"></h3>
                                <button type="button" id="nextMonth" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Day Headers -->
                            <div class="grid grid-cols-7 border-b calendar-day-header">
                                <div class="py-2 text-center text-xs font-semibold">Sun</div>
                                <div class="py-2 text-center text-xs font-semibold">Mon</div>
                                <div class="py-2 text-center text-xs font-semibold">Tue</div>
                                <div class="py-2 text-center text-xs font-semibold">Wed</div>
                                <div class="py-2 text-center text-xs font-semibold">Thu</div>
                                <div class="py-2 text-center text-xs font-semibold">Fri</div>
                                <div class="py-2 text-center text-xs font-semibold">Sat</div>
                            </div>

                            <!-- Calendar Grid -->
                            <div id="calendarGrid" class="grid grid-cols-7 gap-px p-px calendar-grid">
                                <!-- Days will be populated by JavaScript -->
                            </div>
                        </div>

                        <!-- Hidden date input for form submission -->
                        <input type="hidden" id="booking_date" name="booking_date" required>

                        <!-- Selected Date Display -->
                        <div id="selectedDateDisplay" class="hidden mt-3 p-3 bg-kampung-gold/10 rounded-lg">
                            <p class="text-sm">
                                <span class="font-medium text-kampung-brown">Selected:</span>
                                <span id="selectedDateText" class="theme-text-accent font-medium"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Session Row with Availability -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">

                        <!-- Session -->
                        <div>
                            <label for="session_id" class="form-label">
                                Session <span class="text-red-500">*</span>
                            </label>
                            <select id="session_id" name="session_id" class="form-input" required>
                                <option value="">Select session</option>
                            </select>
                        </div>

                        <!-- Availability Indicator (Side by Side) -->
                        <div id="availabilityIndicator" class="hidden">
                            <label class="form-label">Slot Available</label>
                            <div class="bg-white border-2 border-gray-200 rounded-xl p-4 shadow-sm">
                                <div class="flex items-center gap-3 mb-2">
                                    <span id="availabilityIcon" class="w-4 h-4 rounded-full animate-pulse"></span>
                                    <span id="availabilityText" class="font-semibold text-base"></span>
                                </div>
                                <div id="availabilityBar" class="h-3 bg-gray-200 rounded-full overflow-hidden">
                                    <div id="availabilityProgress" class="h-full transition-all duration-500 rounded-full"></div>
                                </div>
                                <p id="availabilitySubtext" class="text-xs text-gray-500 mt-2"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Time Slot -->
                    <div>
                        <label for="time_slot_id" class="form-label">
                            Preferred Time <span class="text-red-500">*</span>
                        </label>
                        <select id="time_slot_id" name="time_slot_id" class="form-input" required disabled>
                            <option value="">Select a session first</option>
                        </select>
                    </div>

                    <!-- Floor Preference -->
                    <div>
                        <label for="floor_preference" class="form-label">
                            Preferred Floor
                        </label>
                        <select id="floor_preference" name="floor_preference" class="form-input">
                            <option value="any">Any Floor (Best Available)</option>
                            <option value="ground">Ground Floor</option>
                            <option value="upper">Upper Floor</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Subject to availability</p>
                    </div>

                    <!-- Party Size -->
                    <div>
                        <label for="pax" class="form-label">
                            Number of Guests <span class="text-red-500">*</span>
                        </label>
                        <select id="pax" name="pax" class="form-input" required>
                            <option value="">Select party size</option>
                            <?php for ($i = MIN_PAX; $i <= MAX_PAX; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> <?= $i === 1 ? 'Guest' : 'Guests' ?></option>
                            <?php endfor; ?>
                        </select>
                        <p class="text-sm text-gray-500 mt-1">
                            For parties larger than <?= MAX_PAX ?>, please call us directly.
                        </p>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 py-2"></div>

                    <!-- Contact Details -->
                    <h3 class="font-heading text-xl font-semibold text-kampung-charcoal">
                        Contact Details
                    </h3>

                    <!-- Name -->
                    <div>
                        <label for="name" class="form-label">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-input"
                            placeholder="Enter your full name"
                            required>
                    </div>

                    <!-- Phone & Email Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Phone -->
                        <div>
                            <label for="phone" class="form-label">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                class="form-input"
                                placeholder="+60 12-345 6789"
                                required>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="form-label">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input"
                                placeholder="your@email.com"
                                required>
                        </div>
                    </div>

                    <!-- Special Requests -->
                    <div>
                        <label for="special_requests" class="form-label">
                            Special Requests (Optional)
                        </label>
                        <textarea
                            id="special_requests"
                            name="special_requests"
                            rows="3"
                            class="form-input"
                            placeholder="Any dietary requirements, special occasions, or preferences..."></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full py-4 rounded-full bg-kampung-brown text-white font-semibold text-lg hover:bg-kampung-brown-dark transition-all duration-300 transform hover:scale-[1.02] flex items-center justify-center gap-2">
                        <span>Confirm Reservation</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>

                </div>
            </form>
        </div>

        <!-- Confirmation Section (Hidden by default) -->
        <div id="confirmationSection" class="hidden bg-kampung-cream rounded-3xl p-8 md:p-12 shadow-xl text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="font-heading text-3xl font-bold text-kampung-charcoal mb-4">
                Reservation Submitted!
            </h2>
            <p class="text-gray-600 mb-6">
                Thank you for choosing Rembayung. We have received your reservation request
                and will contact you shortly to confirm your booking.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= BASE_URL ?>/" class="px-6 py-3 rounded-full border-2 border-kampung-brown text-kampung-brown font-semibold hover:bg-kampung-brown hover:text-white transition-all">
                    Back to Home
                </a>
                <button onclick="location.reload()" class="px-6 py-3 rounded-full bg-kampung-brown text-white font-semibold hover:bg-kampung-brown-dark transition-all">
                    Make Another Reservation
                </button>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
            <!-- Confirmation -->
            <div class="bg-kampung-cream-dark rounded-2xl p-6 text-center scroll-reveal stagger-1">
                <div class="w-12 h-12 bg-kampung-gold/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-kampung-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-kampung-charcoal mb-2">Instant Confirmation</h4>
                <p class="text-sm text-gray-500">We'll confirm your booking within 2 hours</p>
            </div>

            <!-- Free Cancellation -->
            <div class="bg-kampung-cream-dark rounded-2xl p-6 text-center scroll-reveal stagger-2">
                <div class="w-12 h-12 bg-kampung-gold/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-kampung-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-kampung-charcoal mb-2">Free Cancellation</h4>
                <p class="text-sm text-gray-500">Cancel up to 24 hours before your booking</p>
            </div>

            <!-- Support -->
            <div class="bg-kampung-cream-dark rounded-2xl p-6 text-center scroll-reveal stagger-3">
                <div class="w-12 h-12 bg-kampung-gold/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-kampung-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-kampung-charcoal mb-2">Need Help?</h4>
                <p class="text-sm text-gray-500">Call us at <?= RESTAURANT_PHONE ?></p>
            </div>
        </div>

    </div>
</section>

<script>
    const BASE_URL = '<?= BASE_URL ?>';
    let sessionsData = [];
    let availabilityData = {};
    let calendarData = {};
    let currentMonth = new Date();
    let selectedDate = null;

    const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    // Helper function to format date as YYYY-MM-DD without timezone issues
    function formatDateLocal(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadSessions();
        setupFormHandler();
        initCalendar();
        setupCalendarNavigation();
    });

    // ============ CALENDAR FUNCTIONS ============

    function initCalendar() {
        // Start from tomorrow (minimum booking date)
        currentMonth = new Date();
        currentMonth.setDate(1);
        renderCalendar();
    }

    function setupCalendarNavigation() {
        document.getElementById('prevMonth').addEventListener('click', () => {
            currentMonth.setMonth(currentMonth.getMonth() - 1);
            renderCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentMonth.setMonth(currentMonth.getMonth() + 1);
            renderCalendar();
        });
    }

    async function renderCalendar() {
        const year = currentMonth.getFullYear();
        const month = currentMonth.getMonth();

        // Update title
        document.getElementById('calendarTitle').textContent = `${MONTHS[month]} ${year}`;

        // Calculate date range for this month
        const startDate = new Date(year, month, 1);
        const endDate = new Date(year, month + 1, 0);

        // Fetch availability data
        await fetchCalendarData(
            formatDateLocal(startDate),
            formatDateLocal(endDate)
        );

        // Build calendar grid
        const grid = document.getElementById('calendarGrid');
        grid.innerHTML = '';

        // Get first day of month (0 = Sunday)
        const firstDay = startDate.getDay();
        const daysInMonth = endDate.getDate();

        // Today for comparison
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Minimum booking date (tomorrow)
        const minDate = new Date();
        minDate.setDate(minDate.getDate() + 1);
        minDate.setHours(0, 0, 0, 0);

        // Maximum booking date (30 days ahead)
        const maxDate = new Date();
        maxDate.setDate(maxDate.getDate() + 30);
        maxDate.setHours(0, 0, 0, 0);

        // Add empty cells for days before first of month
        for (let i = 0; i < firstDay; i++) {
            grid.appendChild(createEmptyCell());
        }

        // Add day cells
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            date.setHours(0, 0, 0, 0);
            const dateStr = formatDateLocal(date);

            const dayData = calendarData[dateStr] || {
                status: 'available',
                statusLabel: 'Available'
            };

            // Check if date is in valid range
            const isPast = date < minDate;
            const isTooFar = date > maxDate;
            const isDisabled = isPast || isTooFar;

            grid.appendChild(createDayCell(day, dateStr, dayData, isDisabled, date.getTime() === today.getTime()));
        }

        // Add empty cells to complete the grid (6 rows)
        const totalCells = firstDay + daysInMonth;
        const remainingCells = (7 - (totalCells % 7)) % 7;
        for (let i = 0; i < remainingCells; i++) {
            grid.appendChild(createEmptyCell());
        }
    }

    async function fetchCalendarData(startDate, endDate) {
        try {
            const response = await fetch(`${BASE_URL}/api/get_calendar.php?start=${startDate}&end=${endDate}`);
            const result = await response.json();

            if (result.success) {
                calendarData = {};
                result.data.forEach(day => {
                    calendarData[day.date] = day;
                });
            }
        } catch (error) {
            console.error('Error fetching calendar data:', error);
        }
    }

    function createEmptyCell() {
        const cell = document.createElement('div');
        cell.className = 'p-2 min-h-[50px] calendar-cell empty';
        return cell;
    }

    function createDayCell(day, dateStr, dayData, isDisabled, isToday) {
        const cell = document.createElement('div');

        // Base classes
        let classes = 'p-2 min-h-[50px] relative transition-all duration-200 calendar-cell ';

        if (isDisabled) {
            classes += 'opacity-40 cursor-not-allowed disabled ';
        } else {
            classes += 'cursor-pointer ';
        }

        // Selected state
        if (selectedDate === dateStr) {
            classes += 'ring-2 ring-kampung-gold selected ';
        }

        cell.className = classes;

        // Day number
        const dayNum = document.createElement('span');
        dayNum.className = `text-sm font-medium calendar-day-number ${isToday ? 'text-kampung-gold' : ''}`;
        dayNum.textContent = day;

        // Status indicator
        const indicator = document.createElement('span');
        indicator.className = 'absolute bottom-1 right-1 w-2 h-2 rounded-full ';

        if (!isDisabled) {
            switch (dayData.status) {
                case 'available':
                    indicator.className += 'bg-green-500';
                    indicator.title = 'Available';
                    break;
                case 'limited':
                    indicator.className += 'bg-yellow-500';
                    indicator.title = dayData.statusLabel;
                    break;
                case 'full':
                    indicator.className += 'bg-red-500';
                    indicator.title = 'Fully Booked';
                    break;
                case 'closed':
                    indicator.className += 'bg-orange-500';
                    indicator.title = dayData.statusLabel || 'Closed';
                    break;
                default:
                    indicator.className += 'bg-gray-300';
            }
        }

        cell.appendChild(dayNum);
        if (!isDisabled) {
            cell.appendChild(indicator);
        }

        // Click handler
        if (!isDisabled && dayData.status !== 'full' && dayData.status !== 'closed') {
            cell.addEventListener('click', () => selectDate(dateStr, dayData));
        } else if (!isDisabled && (dayData.status === 'full' || dayData.status === 'closed')) {
            cell.addEventListener('click', () => {
                const message = dayData.status === 'full' ?
                    'This date is fully booked. Please select another date.' :
                    `Restaurant is closed: ${dayData.statusLabel}`;
                alert(message);
            });
        }

        return cell;
    }

    function selectDate(dateStr, dayData) {
        selectedDate = dateStr;

        // Update hidden input
        document.getElementById('booking_date').value = dateStr;

        // Update display
        const displayDiv = document.getElementById('selectedDateDisplay');
        const displayText = document.getElementById('selectedDateText');

        const date = new Date(dateStr + 'T00:00:00');
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        displayText.textContent = date.toLocaleDateString('en-MY', options);

        displayDiv.classList.remove('hidden');

        // Re-render calendar to show selection
        renderCalendar();

        // Check availability for session
        checkAvailability();
    }

    // ============ SESSION FUNCTIONS ============

    async function loadSessions() {
        try {
            const response = await fetch(`${BASE_URL}/api/get_sessions.php`);
            const result = await response.json();

            if (result.success && result.data.length > 0) {
                sessionsData = result.data;
                populateSessionDropdown();
            } else {
                console.error('No sessions available');
            }
        } catch (error) {
            console.error('Error loading sessions:', error);
        }
    }

    function populateSessionDropdown() {
        const sessionSelect = document.getElementById('session_id');
        sessionSelect.innerHTML = '<option value="">Select session</option>';

        sessionsData.forEach(session => {
            const option = document.createElement('option');
            option.value = session.id;
            option.textContent = session.name;
            if (session.description) {
                option.textContent += ` (${session.description})`;
            }
            sessionSelect.appendChild(option);
        });

        sessionSelect.addEventListener('change', function() {
            handleSessionChange();
            checkAvailability();
        });
    }

    function handleSessionChange() {
        const sessionId = document.getElementById('session_id').value;
        const timeSlotSelect = document.getElementById('time_slot_id');

        if (!sessionId) {
            timeSlotSelect.innerHTML = '<option value="">Select a session first</option>';
            timeSlotSelect.disabled = true;
            hideAvailability();
            return;
        }

        const session = sessionsData.find(s => s.id === sessionId);
        if (!session || !session.time_slots.length) {
            timeSlotSelect.innerHTML = '<option value="">No time slots available</option>';
            timeSlotSelect.disabled = true;
            return;
        }

        timeSlotSelect.innerHTML = '<option value="">Select time</option>';
        session.time_slots.forEach(slot => {
            const option = document.createElement('option');
            option.value = slot.id;
            option.textContent = slot.time_label;
            timeSlotSelect.appendChild(option);
        });

        timeSlotSelect.disabled = false;
    }

    // ============ AVAILABILITY FUNCTIONS ============

    async function checkAvailability() {
        const date = document.getElementById('booking_date').value;
        const sessionId = document.getElementById('session_id').value;

        if (!date || !sessionId) {
            hideAvailability();
            return;
        }

        try {
            const response = await fetch(`${BASE_URL}/api/get_availability.php?date=${date}&session_id=${sessionId}`);
            const result = await response.json();

            if (result.success && result.data.length > 0) {
                availabilityData = result.data[0];
                displayAvailability(availabilityData);
            } else {
                hideAvailability();
            }
        } catch (error) {
            console.error('Error checking availability:', error);
            hideAvailability();
        }
    }

    function hideAvailability() {
        document.getElementById('availabilityIndicator').classList.add('hidden');
    }

    function displayAvailability(availability) {
        const indicator = document.getElementById('availabilityIndicator');
        const icon = document.getElementById('availabilityIcon');
        const text = document.getElementById('availabilityText');
        const progress = document.getElementById('availabilityProgress');
        const subtext = document.getElementById('availabilitySubtext');

        const {
            remaining_pax,
            max_pax,
            booked_pax
        } = availability;
        const percentage = Math.round((remaining_pax / max_pax) * 100);

        let colorClass, bgClass, borderClass;
        if (percentage > 50) {
            colorClass = 'bg-green-500';
            bgClass = 'text-green-600';
            borderClass = 'border-green-300';
        } else if (percentage > 20) {
            colorClass = 'bg-yellow-500';
            bgClass = 'text-yellow-600';
            borderClass = 'border-yellow-300';
        } else if (percentage > 0) {
            colorClass = 'bg-orange-500';
            bgClass = 'text-orange-600';
            borderClass = 'border-orange-300';
        } else {
            colorClass = 'bg-red-500';
            bgClass = 'text-red-600';
            borderClass = 'border-red-300';
        }

        // Update container border color
        const container = indicator.querySelector('.bg-white');
        if (container) {
            container.className = `bg-white border-2 ${borderClass} rounded-xl p-4 shadow-sm`;
        }

        icon.className = `w-4 h-4 rounded-full animate-pulse ${colorClass}`;
        progress.className = `h-full transition-all duration-500 rounded-full ${colorClass}`;
        progress.style.width = `${percentage}%`;

        // Main text - more prominent
        if (remaining_pax === 0) {
            text.innerHTML = `<span class="${bgClass}">Fully Booked!</span>`;
            subtext.textContent = 'Please select another session or date';
        } else if (remaining_pax <= 20) {
            text.innerHTML = `<span class="${bgClass}">Only ${remaining_pax} pax left!</span>`;
            subtext.textContent = `${booked_pax} of ${max_pax} pax booked - Book now!`;
        } else {
            text.innerHTML = `<span class="${bgClass}">${remaining_pax} pax available</span>`;
            subtext.textContent = `${booked_pax} of ${max_pax} pax booked`;
        }

        indicator.classList.remove('hidden');
    }

    // ============ FORM HANDLER ============

    function setupFormHandler() {
        const form = document.getElementById('bookingForm');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validate date selected
            if (!selectedDate) {
                alert('Please select a date from the calendar.');
                return;
            }

            // Check if session is fully booked
            if (availabilityData.remaining_pax === 0) {
                alert('Sorry, this session is fully booked. Please select a different date or session.');
                return;
            }

            // Check if requested pax exceeds availability
            const requestedPax = parseInt(document.getElementById('pax').value);
            if (requestedPax > availabilityData.remaining_pax) {
                alert(`Sorry, only ${availabilityData.remaining_pax} pax available for this session. Please reduce party size or choose another session.`);
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span>Processing...</span>
        `;

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    document.getElementById('bookingFormContainer').classList.add('hidden');
                    document.getElementById('confirmationSection').classList.remove('hidden');
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Submission error:', error);
                alert('Failed to submit booking. Please try again.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>