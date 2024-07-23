document.addEventListener('DOMContentLoaded', function () {
    fetch('/js/menu.json')
        .then(response => response.json())
        .then(data => {
            const menuItemsContainer = document.getElementById('menuItems');
            data.forEach(item => {
                const menuItem = createMenuItem(item);
                menuItemsContainer.appendChild(menuItem);
            });
        })
        .catch(error => console.error('Error al cargar el menú:', error));
});

function createMenuItem(item) {
    console.log("Agregando item: "+item.name);
    const li = document.createElement('li');
    li.className = 'nav-item';

    if (item.submenu && Array.isArray(item.submenu) && item.submenu.length > 0) {
        console.log("Se valido que tiene un submenu");
        li.className += ' dropdown';
        const a = document.createElement('a');
        a.className = 'nav-link dropdown-toggle';
        a.href = item.link || '#';
        a.id = `navbarDropdown${item.name.replace(/\s/g, '')}`;
        a.role = 'button';
        a.dataset.bsToggle = 'dropdown';
        a.ariaExpanded = 'false';
        a.textContent = item.name;

        const ul = document.createElement('ul');
        ul.className = 'dropdown-menu submenu';
        ul.ariaLabelledby = a.id;
        item.submenu.forEach(subItem => {
            const subMenuItem = createMenuItem(subItem);
            const hr = document.createElement('hr');
            ul.appendChild(subMenuItem);
            ul.appendChild(hr);
        });

        li.appendChild(a);
        li.appendChild(ul);

    } else {
        console.log("No tiene submenu");
        const a = document.createElement('a');
        a.className = 'nav-link';
        a.href = item.link;
        a.textContent = item.name;
        li.appendChild(a);
    }

    return li;
}

document.addEventListener("DOMContentLoaded", function(){
    // make it as accordion for smaller screens
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
    // end if innerWidth
    }); 
    // DOMContentLoaded  end
