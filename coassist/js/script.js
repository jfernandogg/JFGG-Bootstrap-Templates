document.addEventListener('DOMContentLoaded', function () {
    fetch('/coassist/js/menu.json')
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
        ul.className = 'dropdown-menu';
        ul.ariaLabelledby = a.id;

        item.submenu.forEach(subItem => {
            const subMenuItem = createMenuItem(subItem);
            ul.appendChild(subMenuItem);
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
