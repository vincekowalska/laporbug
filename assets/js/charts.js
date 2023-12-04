const ctx = document.getElementById('myChart');
const approved = document.getElementById("approved_findings");
const rejected = document.getElementById("rejected_findings");
const submitted = document.getElementById("submitted_findings");

if(submitted.value != 0 | approved.value != 0 | rejected.value != 0 ){
    data = {
        labels: ['Accepted','Rejected','Submitted'],
        datasets: [{
            label: 'Precentage',
            data: [approved.value, rejected.value, submitted.value],
            backgroundColor: ['rgb(0, 128, 0)','rgb(194, 24, 7)','rgb(255, 255, 0)'],
        hoverOffset: 4}]
    }
}else{
    data = {
        labels: ["No Data"],
        datasets: [{
            label: 'Precentage',
            data: [100],
            backgroundColor: ['rgb(0, 0, 0)'],
        hoverOffset: 4}]
    }
}

new Chart(ctx, {
    type: 'doughnut',
    data: data
});
