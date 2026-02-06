document.addEventListener('DOMContentLoaded', function() {
    console.log("Dashboard.js has loaded successfully!");

    // --- 1. CONFIGURATION ---
    const API_KEY = 'AIzaSyBy-yBgSizYEoy3QL6zoV9qHI0rSEfSAw0'; 
    const CALENDAR_ID = 'kucingcomel56789@gmail.com'; 

    // --- 2. SIDEBAR & MODAL LOGIC ---
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileToggle = document.getElementById('mobileSidebarToggle');
    const modal = document.getElementById('createModal');
    const createBtn = document.getElementById('createBtn');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');

    if (sidebarToggle) sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('collapsed'));
    if (mobileToggle) mobileToggle.addEventListener('click', () => sidebar.classList.toggle('mobile-active'));
    if (createBtn) createBtn.addEventListener('click', () => modal.classList.add('active'));
    if (closeModal) closeModal.addEventListener('click', () => modal.classList.remove('active'));
    if (cancelBtn) cancelBtn.addEventListener('click', () => modal.classList.remove('active'));

    // --- 3. CREATE TASK FORM HANDLING ---
    const createForm = document.getElementById('createForm');
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(createForm);
            
            fetch('save_task.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    modal.classList.remove('active');
                    createForm.reset();
                    window.location.reload();
                } else {
                    alert('Error saving task: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // --- 4. CALENDAR LOGIC ---
    const calendarGrid = document.getElementById('calendarGrid');
    const calendarMonth = document.getElementById('calendarMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    
    if (calendarGrid) {
        let currentDate = new Date(); 
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        const dayNames = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];

        async function fetchAllEvents() {
            if(calendarMonth) calendarMonth.textContent = `${monthNames[currentMonth]} ${currentYear}`;
            
            const timeMin = new Date(currentYear, currentMonth, 1).toISOString();
            const timeMax = new Date(currentYear, currentMonth + 1, 0).toISOString();
            const googleUrl = `https://www.googleapis.com/calendar/v3/calendars/${encodeURIComponent(CALENDAR_ID)}/events?key=${API_KEY}&timeMin=${timeMin}&timeMax=${timeMax}&singleEvents=true`;
            
            // FIX 4: Removed 'php/' prefix
            const localUrl = `get_events.php?month=${currentMonth + 1}&year=${currentYear}`;

            try {
                const [googleRes, localRes] = await Promise.allSettled([fetch(googleUrl), fetch(localUrl)]);
                let combinedEvents = [];

                if (googleRes.status === 'fulfilled' && googleRes.value.ok) {
                    const googleData = await googleRes.value.json();
                    if (googleData.items) combinedEvents = combinedEvents.concat(googleData.items.map(item => ({
                        title: item.summary,
                        date: item.start.date || (item.start.dateTime ? item.start.dateTime.split('T')[0] : ''),
                        type: 'google'
                    })));
                }

                if (localRes.status === 'fulfilled' && localRes.value.ok) {
                    try {
                        const localData = await localRes.value.json();
                        if (Array.isArray(localData)) combinedEvents = combinedEvents.concat(localData.map(item => ({ ...item, type: 'local' })));
                    } catch(e) {}
                }
                renderCalendar(currentMonth, currentYear, combinedEvents);
            } catch (error) { renderCalendar(currentMonth, currentYear, []); }
        }

        function renderCalendar(month, year, eventsList) {
            calendarGrid.innerHTML = '';
            dayNames.forEach(day => {
                const el = document.createElement('div');
                el.className = 'calendar-day-header';
                el.textContent = day;
                calendarGrid.appendChild(el);
            });
            
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const daysInPrevMonth = new Date(year, month, 0).getDate();
            
            for (let i = firstDay - 1; i >= 0; i--) calendarGrid.appendChild(createDayCell(daysInPrevMonth - i, true, []));
            
            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                calendarGrid.appendChild(createDayCell(day, false, eventsList.filter(e => e.date === dateStr)));
            }
        }

        function createDayCell(day, isOtherMonth, dayEvents) {
            const el = document.createElement('div');
            el.className = 'calendar-day';
            if (isOtherMonth) el.classList.add('other-month');
            el.textContent = day;
            const today = new Date();
            if (!isOtherMonth && day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear()) el.classList.add('today');

            if (dayEvents.length > 0) {
                el.classList.add('has-event');
                if (dayEvents.some(e => e.type === 'google')) el.classList.add('google-event');
                if (dayEvents.some(e => e.type === 'local')) el.classList.add('local-event');
                el.title = dayEvents.map(e => e.title).join('\n');
            }
            return el;
        }

        if (prevMonthBtn) prevMonthBtn.addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) { currentMonth = 11; currentYear--; }
            fetchAllEvents();
        });
        
        if (nextMonthBtn) nextMonthBtn.addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) { currentMonth = 0; currentYear++; }
            fetchAllEvents();
        });

        fetchAllEvents();
    }
});

// --- 5. COMPLETE TASK LOGIC (Global) ---
window.completeTask = function(taskId) {
    // FIX 5: Removed 'php/' prefix
    fetch('update_task.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: taskId, status: 'completed' })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const checkbox = document.getElementById('task_' + taskId);
            if(checkbox) checkbox.closest('.task-item').style.opacity = '0.5';
            setTimeout(() => window.location.reload(), 300);
        }
    })
    .catch(error => console.error('Error:', error));
};