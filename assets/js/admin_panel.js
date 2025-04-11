$(document).ready(function () {
    getUserList(1);
});

document.getElementById('searchInput').addEventListener('keyup', filterUsers);
document.getElementById('superadminCheck').addEventListener('change', filterUsers);
document.getElementById('adminCheck').addEventListener('change', filterUsers);
document.getElementById('modCheck').addEventListener('change', filterUsers);
document.getElementById('userCheck').addEventListener('change', filterUsers);

function filterUsers() {
    const filter = document.getElementById('searchInput').value.toLowerCase();
    const isSuperadminChecked = document.getElementById('superadminCheck').checked;
    const isAdminChecked = document.getElementById('adminCheck').checked;
    const isModChecked = document.getElementById('modCheck').checked;
    const isUserChecked = document.getElementById('userCheck').checked;

    const filters = {
        search: filter,
        superadmin: isSuperadminChecked,
        admin: isAdminChecked,
        mod: isModChecked,
        user: isUserChecked
    };

    getUserList(1, filters);
}

function getUserList(page, filters = {}) {
    const newUrl = "admin_panel?page=" + page;
    window.history.pushState({}, '', newUrl);

    fetch(newUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            action: 'getList',
            search: filters.search || '',
            superadmin: filters.superadmin ? 1 : 0,
            admin: filters.admin ? 1 : 0,
            mod: filters.mod ? 1 : 0,
            user: filters.user ? 1 : 0
        })
    })
    .then(response => response.json()) 
    .then(data => {
        if (data.success) {
            // console.log('Response data:', data); 
            showTable(data.result);
            showPagination(data.totalPages, page, filters);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function showTable(data) {
    let tableBody = document.querySelector('#data-table tbody');
    tableBody.innerHTML = '';

    if (data.length === 0) {
        let tr = document.createElement('tr');
        tr.innerHTML = `
            <td colspan="5"><span>No User Found.</span></td>
        `;
        tableBody.appendChild(tr);
    } else {
        data.forEach((row) => {
            let tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${row.user_id}</td>
                <td>${row.username || '-'}</td>
                <td>${row.email || '-'}</td>
                <td>${row.roles || '-'}</td>
                <td style="padding: 0">
                    <a href="manage_user?user_id=${row.user_id}"><i class="bi bi-arrow-right-circle"></i></a>
                </td>
            `;  
   
            tableBody.appendChild(tr);
        });
    }
}

function showPagination(totalPages, currentPage, filters) {
    const paginationContainer = document.getElementById('user-pagination-container');
    paginationContainer.innerHTML = '';

    //First Page
    const firstButton = document.createElement('button');
    firstButton.innerHTML = '<i class="bi bi-chevron-bar-left"></i>';
    firstButton.disabled = totalPages === 0 || currentPage === 1;
    firstButton.addEventListener('click', () => getUserList(1, filters));
    paginationContainer.appendChild(firstButton);

    //Previous Page
    const prevButton = document.createElement('button');
    prevButton.innerHTML = '<i class="bi bi-chevron-left"></i>';
    prevButton.disabled = totalPages === 0 || currentPage === 1;
    prevButton.addEventListener('click', () => getUserList(currentPage - 1, filters));
    paginationContainer.appendChild(prevButton);

    //Page Numbers
    if (totalPages === 0) {
        const button = document.createElement('button');
        button.textContent = '1';
        button.className = 'active current-button';
        paginationContainer.appendChild(button);
    } else {
        const range = 5;
        let startPage = Math.max(currentPage - Math.floor(range / 2), 1);
        let endPage = Math.min(startPage + range - 1, totalPages);
        if (endPage - startPage < range - 1) {
            startPage = Math.max(endPage - range + 1, 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            button.className = i === currentPage ? 'active current-button' : '';
            button.addEventListener('click', () => getUserList(i, filters));
            paginationContainer.appendChild(button);
        }
    }

    //Next Page
    const nextButton = document.createElement('button');
    nextButton.innerHTML = '<i class="bi bi-chevron-right"></i>';
    nextButton.disabled = totalPages === 0 || currentPage === totalPages;
    nextButton.addEventListener('click', () => getUserList(currentPage + 1, filters));
    paginationContainer.appendChild(nextButton);

    //Last Page
    const lastButton = document.createElement('button');
    lastButton.innerHTML = '<i class="bi bi-chevron-bar-right"></i>';
    lastButton.disabled = totalPages === 0 || currentPage === totalPages;
    lastButton.addEventListener('click', () => getUserList(totalPages, filters));
    paginationContainer.appendChild(lastButton);
}


