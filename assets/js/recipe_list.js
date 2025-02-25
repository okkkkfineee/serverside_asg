$(document).ready(function () {
    $('#customer').select2({theme: "bootstrap-5"});
    $('#PIC').select2({theme: "bootstrap-5"});
    $('#AT').select2({theme: "bootstrap-5"});
    $('#account').select2({theme: "bootstrap-5"});
    applyFilter(1);
});

$(function() {
    $("#filter-clear").click(function() {
        $("#customer").val('').trigger('change');
        $("#PIC").val('').trigger('change');
        $("#AT").val('').trigger('change');
        $("#account").val('').trigger('change');
    });
});

document.getElementById('filterForm').addEventListener('submit', function(e) {
    e.preventDefault(); 
    applyFilter(1);
});

function applyFilter(page) {
    const TRtype = document.getElementById('TRtype').value;
    const customer = document.getElementById('customer') ? document.getElementById('customer').value : '';
    const account = document.getElementById('account') ? document.getElementById('account').value : '';
    const amountMin = document.getElementById('amountMin') ? document.getElementById('amountMin').value : '';
    const amountMax = document.getElementById('amountMax') ? document.getElementById('amountMax').value : '';
    const PIC = document.getElementById('PIC') ? document.getElementById('PIC').value : '';
    const AT = document.getElementById('AT') ? document.getElementById('AT').value : '';
    const from = document.getElementById('from') ? document.getElementById('from').value : '';
    const to = document.getElementById('to') ? document.getElementById('to').value : '';

    const newUrl = "recipe_list?page=" + page;
    window.history.pushState({}, '', newUrl);
    fetch(newUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            action: 'apply',
            TRtype: TRtype,
            customer: customer,
            account: account,
            amountMin: amountMin,
            amountMax: amountMax,
            PIC: PIC,
            AT: AT,
            from: from,
            to: to
        }) 
    })
    .then(response => response.json()) 
    .then(data => {
        if (data.success) {
            // console.log('Response data:', data); 
            showTable(data.result, TRtype, page);
            showPagination(data.totalPages, page);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function showTable(data, TRtype, page) {
    let tableBody = document.querySelector('#data-table tbody');
    tableBody.innerHTML = '';
    let limit = 20;  // Number of records per page (adjust in trList.js aswell)
    let startCount = (page - 1) * limit + 1;

    if (data.length === 0) {
        let tr = document.createElement('tr');
        if (TRtype === 'revenue') {
            tr.innerHTML = `
                <td colspan="9"><span>No Transaction Found.</span></td>
            `;
        } else if (TRtype === 'expenses') {
            tr.innerHTML = `
                <td colspan="8"><span>No Transaction Found.</span></td>
            `;
        }
        tableBody.appendChild(tr);
    } else {
        data.forEach((row, index) => {
            let tr = document.createElement('tr');
            let count = startCount + index;
            if (TRtype === 'revenue') {
                tr.innerHTML = `
                    <td>${count}</td>
                    <td>${row.UID}</td>
                    <td>${row.PO_Date || '-'}</td>
                    <td class="text-start">${row.Project || '-'}</td>
                    <td>${row.Customer || ''}</td>
                    <td>${row.PIC1 || '-'}</td>
                    <td>${row.totalPICCount || '-'}</td>
                    <td>${row.PO || '-'}</td>
                    <td><a href="ViewTR.php?TRtype=${TRtype}&UID=${row.UID}" 
                        class="btn btn-link" style="text-decoration: none; color: black;">
                        <span><i class="bi bi-arrow-right-circle"></i></span>
                    </a></td>
                `;  
            } else if (TRtype === 'expenses') {
                tr.innerHTML = `
                    <td>${count}</td>
                    <td>${row.Sequence}</td>
                    <td>${row.Account_Description || '-'}</td>
                    <td>${row.Date || '-'}</td>
                    <td class="text-start">${(row.Allocation_Transaction && row.Allocation_Transaction.trim()) ? row.Allocation_Transaction : '-'}</td>
                    <td>${row.Debit_Domestic || '-'}</td>
                    <td>${row.Credit_Domestic || '-'}</td>
                    <td><a href="ViewTR.php?TRtype=${TRtype}&Sequence=${row.Sequence}" 
                        class="btn btn-link" style="text-decoration: none; color: black;">
                        <span><i class="bi bi-arrow-right-circle"></i></span>
                    </a></td>
                `;  
            }
            tableBody.appendChild(tr);
        });
    }
}

function showPagination(totalPages, currentPage) {
    const paginationContainer = document.getElementById('pagination-container');
    paginationContainer.innerHTML = '';

    //First Page
    const firstButton = document.createElement('button');
    firstButton.innerHTML = '<i class="bi bi-chevron-bar-left"></i>';
    firstButton.disabled = totalPages === 0 || currentPage === 1;
    firstButton.addEventListener('click', () => applyFilter(1));
    paginationContainer.appendChild(firstButton);

    //Previous Page
    const prevButton = document.createElement('button');
    prevButton.innerHTML = '<i class="bi bi-chevron-left"></i>';
    prevButton.disabled = totalPages === 0 || currentPage === 1;
    prevButton.addEventListener('click', () => applyFilter(currentPage - 1));
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
            button.addEventListener('click', () => applyFilter(i));
            paginationContainer.appendChild(button);
        }
    }

    //Next Page
    const nextButton = document.createElement('button');
    nextButton.innerHTML = '<i class="bi bi-chevron-right"></i>';
    nextButton.disabled = totalPages === 0 || currentPage === totalPages;
    nextButton.addEventListener('click', () => applyFilter(currentPage + 1));
    paginationContainer.appendChild(nextButton);

    //Last Page
    const lastButton = document.createElement('button');
    lastButton.innerHTML = '<i class="bi bi-chevron-bar-right"></i>';
    lastButton.disabled = totalPages === 0 || currentPage === totalPages;
    lastButton.addEventListener('click', () => applyFilter(totalPages));
    paginationContainer.appendChild(lastButton);

}


