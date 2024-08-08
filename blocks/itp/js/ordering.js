let form=document.querySelector('#filterformid');
const linkDomStartDate=document.querySelector('#sd-link');
const linkDomEndDate=document.querySelector('#ed-link');
const linkDomAtt=document.querySelector('#at-link');
const linkDomAss=document.querySelector('#as-link');
const linkDomCourse=document.querySelector('#c-link');
const linkDomDuration=document.querySelector('#d-link');
const orderbyField=document.createElement('input');


function startOrdering(Y,order){
    orderbyField.setAttribute('type','hidden');
    orderbyField.setAttribute('name','orderby');

    if (form===null){
        form=document.createElement('form');
        form.setAttribute('method','POST');
        form.setAttribute('action','/my/');
        document.body.appendChild(form);
    }
    
    
    form.appendChild(orderbyField);
    const orderField=document.createElement('input');
    orderField.setAttribute('type','hidden');
    orderField.setAttribute('name','order');
    if (order){
        orderField.setAttribute('value','ASC');
    } else {
        orderField.setAttribute('value','DESC');
    } form.appendChild(orderField);
}

linkDomStartDate.addEventListener('click',x=>{
    x.preventDefault();
    orderbyField.setAttribute('value','startdate');
    if (document.readyState==='complete'){
        form.submit();
    }
});
linkDomEndDate.addEventListener('click',x=>{
    x.preventDefault();
    orderbyField.setAttribute('value','enddate');
    if (document.readyState==='complete'){
        form.submit();
    }
});
linkDomAtt.addEventListener('click',x=>{
    x.preventDefault();
    orderbyField.setAttribute('value','att');
    if (document.readyState==='complete'){
        form.submit();
    }
});
linkDomAss.addEventListener('click',x=>{
    x.preventDefault();
    orderbyField.setAttribute('value','ass');
    if (document.readyState==='complete'){
        form.submit();
    }
});
linkDomCourse.addEventListener('click',x=>{
    x.preventDefault();
    orderbyField.setAttribute('value','course');
    if (document.readyState==='complete'){
        form.submit();
    }
});
linkDomDuration.addEventListener('click',x=>{
    x.preventDefault();
    orderbyField.setAttribute('value','duration');
    if (document.readyState==='complete'){
        form.submit();
    }
});