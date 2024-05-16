export const sortITPTable=()=>{
    const itpTable=document.querySelector('.block_itp_table');
    itpTable.classList.add('table-responsive');
    const linkDomStartDate=document.querySelector('#sd-link');
    const linkDomEndDate=document.querySelector('#ed-link');
    const linkDomAtt=document.querySelector('#at-link');
    const linkDomAss=document.querySelector('#as-link');

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
}