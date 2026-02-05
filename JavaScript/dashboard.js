document.addEventListener('DOMContentLoaded', function() {
    
    console.log("Dashboard.js has loaded successfully!"); // Debug check

    // --- 1. CONFIGURATION ---
    const API_KEY = 'AIzaSyBy-yBgSizYEoy3QL6zoV9qHI0rSEfSAw0';
    const CALENDAR_ID = 'kucingcomel56789@gmail.com'; 

    // --- 2. SIDEBAR LOGIC ---
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });
    }

    // --- 3. CALENDAR ELEMENTS ---
    const calendarGrid = document.getElementById('calendarGrid');
    const calendarMonth = document.getElementById('calendarMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    
    if (!calendarGrid) {
        console.error("CRITICAL: Calendar Grid element not found!");
        return;
    }

    let currentDate = new Date(); 
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const dayNames = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];

    // --- 4. FETCH LOGIC ---
    async function fetchAllEvents() {
        // Update header immediately
        calendarMonth.textContent = `${monthNames[currentMonth]} ${currentYear}`;
        
        const timeMin = new Date(currentYear, currentMonth, 1).toISOString();
        const timeMax = new Date(currentYear, currentMonth + 1, 0).toISOString();

        // API URLs
        const googleUrl = `https://www.googleapis.com/calendar/v3/calendars/${encodeURIComponent(CALENDAR_ID)}/events?key=${API_KEY}&timeMin=${timeMin}&timeMax=${timeMax}&singleEvents=true`;
        
        // IMPORTANT: Because this JS is running from the browser, the path to PHP is relative to the PHP file, NOT the JS file.
        // Since dashboard.php is in /php/, and get_events.php is in /php/, we just use the filename.
        const localUrl = `get_events.php?month=${currentMonth + 1}&year=${currentYear}`;

        try {
            const [googleRes, localRes] = await Promise.allSettled([
                fetch(googleUrl),
                fetch(localUrl)
            ]);

            let combinedEvents = [];

            // Process Google
            if (googleRes.status === 'fulfilled' && googleRes.value.ok) {
                const googleData = await googleRes.value.json();
                if (googleData.items) {
                    const gEvents = googleData.items.map(item => ({
                        title: item.summary,
                        date: item.start.date || (item.start.dateTime ? item.start.dateTime.split('T')[0] : ''),
                        type: 'google'
                    }));
                    combinedEvents = combinedEvents.concat(gEvents);
                }
            }

            // Process Local
            if (localRes.status === 'fulfilled' && localRes.value.ok) {
                try {
                    const localData = await localRes.value.json();
                    if (Array.isArray(localData)) {
                        const lEvents = localData.map(item => ({ ...item, type: 'local' }));
                        combinedEvents = combinedEvents.concat(lEvents);
                    }
                } catch(e) { console.log("Local API silent fail"); }
            }

            renderCalendar(currentMonth, currentYear, combinedEvents);

        } catch (error) {
            console.error("Fetch error:", error);
            renderCalendar(currentMonth, currentYear, []);
        }
    }

    // --- 5. RENDER LOGIC ---
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
        
        for (let i = firstDay - 1; i >= 0; i--) {
            calendarGrid.appendChild(createDayCell(daysInPrevMonth - i, true, []));
        }
        
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dayEvents = eventsList.filter(e => e.date === dateStr);
            calendarGrid.appendChild(createDayCell(day, false, dayEvents));
        }
    }

    function createDayCell(day, isOtherMonth, dayEvents) {
        const el = document.createElement('div');
        el.className = 'calendar-day';
        if (isOtherMonth) el.classList.add('other-month');
        el.textContent = day;

        if (dayEvents.length > 0) {
            el.classList.add('has-event');
            if (dayEvents.some(e => e.type === 'google')) el.classList.add('google-event');
            if (dayEvents.some(e => e.type === 'local')) el.classList.add('local-event');
            el.title = dayEvents.map(e => e.title).join('\n');
        }
        return el;
    }

    // --- 6. LISTENERS ---
    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) { currentMonth = 11; currentYear--; }
            fetchAllEvents();
        });
    }
    
    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) { currentMonth = 0; currentYear++; }
            fetchAllEvents();
        });
    }

    // INITIAL LOAD
    fetchAllEvents();
});