function closeJobNotification() {
    const notification = document.getElementById('job-notification');
    if (notification) {
        notification.classList.remove('show');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    fetch('/js/menu.json')
        .then(response => response.json())
        .then(data => {
            const menuItemsContainer = document.getElementById('menuItems');
            data.forEach(item => {
                const menuItem = createMenuItem(item);
                menuItemsContainer.appendChild(menuItem);
            });
            // Inicializar comportamiento de submenús después de cargar los datos
            initMobileMenu();
        })
        .catch(error => console.error('Error al cargar el menú:', error));

    // Mostrar aviso de vacantes después de 3 segundos
    setTimeout(() => {
        const notification = document.getElementById('job-notification');
        if (notification) {
            notification.classList.add('show');
        }
    }, 3000);
});

function createMenuItem(item, isSubmenu = false) {
    const li = document.createElement('li');
    li.className = isSubmenu ? '' : 'nav-item';

    if (item.submenu && Array.isArray(item.submenu) && item.submenu.length > 0) {
        li.classList.add('dropdown');
        const a = document.createElement('a');
        a.className = isSubmenu ? 'dropdown-item dropdown-toggle' : 'nav-link dropdown-toggle';
        a.href = item.link || '#';
        a.id = `navbarDropdown${item.name.replace(/\s/g, '')}`;
        a.role = 'button';
        a.dataset.bsToggle = 'dropdown';
        a.ariaExpanded = 'false';
        a.textContent = item.name;
        if (item.target) {
            a.target = item.target;
            if (item.target === '_blank') {
                a.rel = 'noopener noreferrer';
            }
        }

        const ul = document.createElement('ul');
        ul.className = 'dropdown-menu submenu';
        ul.ariaLabelledby = a.id;
        item.submenu.forEach(subItem => {
            const subMenuItem = createMenuItem(subItem, true);
            ul.appendChild(subMenuItem);
        });

        li.appendChild(a);
        li.appendChild(ul);

    } else {
        const a = document.createElement('a');
        a.className = isSubmenu ? 'dropdown-item' : 'nav-link';
        a.href = item.link || '#';
        a.textContent = item.name;
        if (item.target) {
            a.target = item.target;
            if (item.target === '_blank') {
                a.rel = 'noopener noreferrer';
            }
        }
        li.appendChild(a);
    }

    return li;
}

function initMobileMenu() {
    if (window.innerWidth < 992) {
      // close all inner dropdowns when parent is closed
      document.querySelectorAll('.navbar .dropdown').forEach(function(everydropdown){
        everydropdown.addEventListener('hidden.bs.dropdown', function () {
          // after dropdown is hidden, then find all submenus
            this.querySelectorAll('.submenu').forEach(function(everysubmenu){
              // hide every submenu as well
              everysubmenu.style.display = 'none';
            });
        })
      });
    
      document.querySelectorAll('.dropdown-menu a').forEach(function(element){
        element.addEventListener('click', function (e) {
            let nextEl = this.nextElementSibling;
            if(nextEl && nextEl.classList.contains('submenu')) {	
              // prevent opening link if link needs to open dropdown
              e.preventDefault();
              if(nextEl.style.display == 'block'){
                nextEl.style.display = 'none';
              } else {
                nextEl.style.display = 'block';
              }
    
            }
        });
      })
    }
}
