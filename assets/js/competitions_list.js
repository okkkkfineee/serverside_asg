$(document).ready(function () {
    getCompList({
        search: '',
        theme: '',
        status: ['ongoing', 'voting', 'ended']
    });
});

window.onload = function() {
    document.getElementById("status_all").checked = true;
    document.getElementById("status_ongoing").checked = true;
    document.getElementById("status_voting").checked = true;
    document.getElementById("status_ended").checked = true;
};

function toggleAllStatus(allCheckbox) {
    const checkboxes = ["status_ongoing", "status_voting", "status_ended"];
    checkboxes.forEach(id => {
        document.getElementById(id).checked = allCheckbox.checked;
    });
}

function toggleStatusAll(individualCheckbox) {
    const allCheckbox = document.getElementById("status_all");
    const checkboxes = ["status_ongoing", "status_voting", "status_ended"];
    
    if (!individualCheckbox.checked) {
        allCheckbox.checked = false;
    } else {
        const allChecked = checkboxes.every(id => document.getElementById(id).checked);
        allCheckbox.checked = allChecked;
    }
}

document.getElementById('comp_name').addEventListener('keyup', applyFilters);
document.getElementById('theme').addEventListener('change', applyFilters);

document.getElementById('status_all').addEventListener('change', applyFilters);
document.getElementById('status_ongoing').addEventListener('change', applyFilters);
document.getElementById('status_voting').addEventListener('change', applyFilters);
document.getElementById('status_ended').addEventListener('change', applyFilters);

function applyFilters() {
    const compName = document.getElementById('comp_name').value.toLowerCase();
    const theme = document.getElementById('theme').value;

    const statusFilters = [];
    if (document.getElementById('status_ongoing').checked) statusFilters.push('ongoing');
    if (document.getElementById('status_voting').checked) statusFilters.push('voting');
    if (document.getElementById('status_ended').checked) statusFilters.push('ended');

    console.log("Competition Name:", compName);
    console.log("Selected Theme:", theme);
    console.log("Selected Status Filters:", statusFilters);

    const filters = {
        search: compName,
        theme: theme,
        status: statusFilters
    };

    getCompList(filters);
}

function getCompList(filters = {}) {
    const newUrl = "competitions_list";
    window.history.pushState({}, '', newUrl);

    const formData = new URLSearchParams({
        action: 'getList',
        search: filters.search || '',
        theme: filters.theme || '',
        status: (filters.status || []).join(',')
    });

    fetch(newUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Response data:', data.result); 
            showList(data.result);
        }
    })

    // .then(response => {
    //     const contentType = response.headers.get("content-type");
    //     if (contentType && contentType.indexOf("application/json") !== -1) {
    //         // If the content type is application/json, decode as json
    //         return response.json()
    //     } else {
    //         // otherwise it can be treated as text
    //         return response.text();
    //     }
    // })
    // .then(data => {
    //     // check if data is an object
    //     if (typeof data === 'object') {
    //         if (data.success) {
    //           console.log('Response data:', data);
    //           // showList(data.result);
    //         }
    //     } else {
    //        console.log('Response text:', data);
    //     }
    // })
     
    .catch(error => {
        console.error('Error:', error);
    });
}

function showList(data) {
    const container = document.getElementById('competition-list');
    container.innerHTML = '';

    if (data.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center">
                <p class="lead">No competitions available.</p>
            </div>`;
        return;
    }

    let row = document.createElement('div');
    row.classList.add('row', 'justify-content-center');

    data.forEach((comp, i) => {
        if (i !== 0 && i % 3 === 0) {
            container.appendChild(row);
            row = document.createElement('div');
            row.classList.add('row', 'mt-4');
        }

        const endDate = new Date(comp.end_date);
        const today = new Date();
        const diffDays = Math.ceil((endDate - today) / (1000 * 60 * 60 * 24));
        let badgeClass = '', timeLeftText = '';

        if (diffDays > 0) {
            timeLeftText = `${diffDays} days left`;
            badgeClass = 'bg-success';
        } else if (diffDays >= -10) {
            timeLeftText = 'Voting Period';
            badgeClass = 'bg-warning';
        } else {
            timeLeftText = 'Ended';
            badgeClass = 'bg-danger';
        }

        const imgSrc = comp.comp_image 
            ? `../uploads/comp/${comp.comp_image}` 
            : '../assets/images/default_comp.png';

        const card = document.createElement('div');
        card.className = 'col-lg-4 col-md-6 col-sm-12 col-xl-4 d-flex justify-content-center mb-4';
        card.innerHTML = `
            <div class="card border shadow-sm" style="width: 100%;">
                <img src="${imgSrc}" class="card-img-top" alt="Competition Image" style="height: 200px; object-fit: cover;">
                <div class="d-flex flex-column card-body justify-content-between p-3 text-start">
                    <h5 class="card-title">${comp.comp_title}</h5>
                    <p class="card-text">${comp.comp_desc.substring(0, 50)}...</p>
                    <div class="row">
                        <div class="col-8">
                            <p class="card-text"><b>Theme: </b>${comp.comp_theme}</p>
                        </div>
                        <div class="col-4">
                            <p>
                                <span class="badge ${badgeClass} px-3 py-2 float-end">
                                    ${timeLeftText}
                                </span>
                            </p>
                        </div>
                    </div>
                    <a href="view_comp?comp_id=${comp.comp_id}#entries" class="btn btn-secondary mt-2" style="color: white;">View Competition</a>
                </div>
            </div>
        `;
        row.appendChild(card);
    });

    container.appendChild(row);
}
