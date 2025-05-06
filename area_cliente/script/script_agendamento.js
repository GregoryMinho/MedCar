  // Initialize Lucide icons
  lucide.createIcons();

  // Calendar functionality
  const currentDate = new Date();
  let currentMonth = currentDate.getMonth();
  let currentYear = currentDate.getFullYear();
  let selectedDate = null;

  const monthSelect = document.getElementById('month-select');
  const yearSelect = document.getElementById('year-select');
  const prevMonthBtn = document.getElementById('prev-month');
  const nextMonthBtn = document.getElementById('next-month');
  const calendarDaysContainer = document.getElementById('calendar-days');

  // Populate year select
  const startYear = currentYear;
  const endYear = currentYear + 3;
  for (let year = startYear; year <= endYear; year++) {
      const option = document.createElement('option');
      option.value = year;
      option.textContent = year;
      yearSelect.appendChild(option);
  }

  // Set default values for month and year selects
  monthSelect.value = currentMonth;
  yearSelect.value = currentYear;

  // Generate calendar
  function generateCalendar(month, year) {
      calendarDaysContainer.innerHTML = '';

      const firstDay = new Date(year, month, 1);
      const lastDay = new Date(year, month + 1, 0);
      const daysInMonth = lastDay.getDate();
      const startingDay = firstDay.getDay(); // 0 = Sunday, 1 = Monday, etc.

      // Previous month days
      const prevMonthLastDay = new Date(year, month, 0).getDate();
      for (let i = startingDay - 1; i >= 0; i--) {
          const dayElement = document.createElement('div');
          dayElement.className = 'calendar-day calendar-day-disabled h-12 flex items-center justify-center rounded-lg';
          dayElement.textContent = prevMonthLastDay - i;
          calendarDaysContainer.appendChild(dayElement);
      }

      // Current month days
      const today = new Date();
      const isCurrentMonth = today.getMonth() === month && today.getFullYear() === year;

      for (let i = 1; i <= daysInMonth; i++) {
          const dayElement = document.createElement('div');
          dayElement.className = 'calendar-day h-12 flex items-center justify-center rounded-lg cursor-pointer';

          // Check if this is today
          if (isCurrentMonth && i === today.getDate()) {
              dayElement.classList.add('bg-blue-100');
          }

          // Check if this is the selected date
          if (selectedDate && selectedDate.getDate() === i &&
              selectedDate.getMonth() === month &&
              selectedDate.getFullYear() === year) {
              dayElement.classList.add('calendar-day-selected');
          }

          dayElement.textContent = i;

          // Add click event
          dayElement.addEventListener('click', () => {
              // Remove selected class from all days
              document.querySelectorAll('.calendar-day-selected').forEach(el => {
                  el.classList.remove('calendar-day-selected');
              });

              // Add selected class to clicked day
              dayElement.classList.add('calendar-day-selected');

              // Update selected date
              selectedDate = new Date(year, month, i);

              // Update hidden input with selected date
              dataSelecionada.value = selectedDate.toISOString().split('T')[0];
          });

          calendarDaysContainer.appendChild(dayElement);
      }

      // Next month days
      const totalCells = 42; // 6 rows of 7 days
      const remainingCells = totalCells - (startingDay + daysInMonth);

      for (let i = 1; i <= remainingCells; i++) {
          const dayElement = document.createElement('div');
          dayElement.className = 'calendar-day calendar-day-disabled h-12 flex items-center justify-center rounded-lg';
          dayElement.textContent = i;
          calendarDaysContainer.appendChild(dayElement);
      }
  }

  // Adjust year when a previous month is selected
  monthSelect.addEventListener('change', () => {
      const selectedMonth = parseInt(monthSelect.value);
      if (selectedMonth < currentMonth && parseInt(yearSelect.value) === currentYear) {
          // If the selected month is earlier than the current month, increment the year
          currentYear++;
          yearSelect.value = currentYear;
      }
      currentMonth = selectedMonth;
      generateCalendar(currentMonth, currentYear);
  });

  // Adjust year when navigating months
  prevMonthBtn.addEventListener('click', () => {
      if (currentMonth > currentDate.getMonth() || currentYear > currentDate.getFullYear()) {
          currentMonth--;
          if (currentMonth < 0) {
              currentMonth = 11;
              currentYear--;
          }
          if (currentMonth < currentDate.getMonth() && currentYear === currentDate.getFullYear()) {
              currentYear++;
          }
          monthSelect.value = currentMonth;
          yearSelect.value = currentYear;
          generateCalendar(currentMonth, currentYear);
      }
  });

  nextMonthBtn.addEventListener('click', () => {
      currentMonth++;
      if (currentMonth > 11) {
          currentMonth = 0;
          currentYear++;
          if (currentYear > endYear) {
              // Reset to current month and year if past the last available year
              currentMonth = currentDate.getMonth();
              currentYear = currentDate.getFullYear();
          }
      }
      monthSelect.value = currentMonth;
      yearSelect.value = currentYear;
      generateCalendar(currentMonth, currentYear);
  });

  // Event listener for year select
  yearSelect.addEventListener('change', () => {
      currentYear = parseInt(yearSelect.value);
      generateCalendar(currentMonth, currentYear);
  });

  // Initialize calendar
  generateCalendar(currentMonth, currentYear);

  // Time slot selection
  const timeSlots = document.querySelectorAll('.time-slot');
  const horarioSelecionado = document.getElementById('horario_selecionado');
  timeSlots.forEach(slot => {
      slot.addEventListener('click', () => {
          // Remove selected class from all slots
          timeSlots.forEach(s => {
              s.classList.remove('bg-teal-50');
              s.classList.remove('border-teal-500');
              s.classList.add('border-gray-300');
          });
          // Add selected class to clicked slot
          slot.classList.remove('border-gray-300');
          slot.classList.add('bg-teal-50');
          slot.classList.add('border-teal-500');
          // Update hidden input with selected time
          horarioSelecionado.value = slot.textContent.trim();
      });
  });

  // Campo oculto para a data selecionada
  const dataSelecionada = document.getElementById('data_consulta');
  // Add click event
  dayElement.addEventListener('click', () => {
      // Remove selected class from all days
      document.querySelectorAll('.calendar-day-selected').forEach(el => {
          el.classList.remove('calendar-day-selected');
      });

      // Add selected class to clicked day
      dayElement.classList.add('calendar-day-selected');

      // Update selected date
      selectedDate = new Date(year, month, i);

      // Update hidden input with selected date
      dataSelecionada.value = selectedDate.toISOString().split('T')[0];
  });

  
  