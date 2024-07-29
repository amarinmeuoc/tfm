import {exception as displayException} from 'core/notification';
import Templates from 'core/templates';
export const loadTemplate = () => {
    requestDataToServer();
    
    const bosubmit=document.querySelector('#id_bosubmit');
    
    bosubmit.addEventListener('click',()=>{
      requestDataToServer();
    })
}

const requestDataToServer= ()=>{
    const customerSel=document.querySelector('#id_customer');
    if (customerSel===null)
        customerid=-1;
    else
        customerid=customerSel.value;
    const role=document.querySelector('#id_role');
    const groupSel=document.querySelector('#id_grouptrainee');
    const selListTrainees=document.querySelector('#fitem_id_list_trainees>div:nth-child(2)>div:nth-child(4)>span');
    let listTrainees="";
    if (selListTrainees!==null)
      if (selListTrainees.hasAttribute('data-value'))
        listTrainees=selListTrainees.attributes['data-value'].value;
    
    let listCourses="";
    const selListCourses=document.querySelector('#fitem_id_list_courses>div:nth-child(2)>div:nth-child(4)>span');
    if (selListCourses!==null)
      if (selListCourses.hasAttribute('data-value'))
        listCourses=selListCourses.attributes['data-value'].value;
   
    const startdate_day=document.querySelector('#id_startdate_day');
    const startdate_month=document.querySelector('#id_startdate_month');
    const startdate_year=document.querySelector('#id_startdate_year');
    const startdate=Math.floor(new Date(startdate_year.value+'.'+startdate_month.value+'.'+startdate_day.value).getTime()/1000);
    const queryType=document.querySelector('input[type="radio"][name="status"]:checked');
     
   
    const token=document.querySelector('input[name="token"]').value;
    const xhr=new XMLHttpRequest();
    const url=window.location.protocol+'//'+window.location.hostname+'/webservice/rest/server.php';
    xhr.open('POST',url,true);
    //const roleValue=(role.value===1)?true:false;
    const formData=new FormData();
    formData.append('wstoken',token);
    formData.append('wsfunction','report_coursereport_get_assessment');
    formData.append('moodlewsrestformat','json');
    formData.append('params[0][role]',role.value);
    formData.append('params[0][customerid]',customerid);
    formData.append('params[0][groupid]',groupSel.value);
    formData.append('params[0][billid]',listTrainees);
    formData.append('params[0][wbs]',listCourses);
    formData.append('params[0][startdate]',startdate);
    formData.append('params[0][offset]',50);
    formData.append('params[0][order]',1);
    formData.append('params[0][orderby]','shortname');
    formData.append('params[0][queryType]',queryType.value);
    formData.append('params[0][page]',1);

    

    setTimeout(()=> {xhr.send(formData);},100);
    xhr.onload = (event) =>{
        onLoadFunction(xhr);
        };
    xhr.onloadstart = (event)=>{
      showLoader(event);
    }
      xhr.onprogress = (event)=>{
        onProgressFunction(event);
      } 
    xhr.onloadend = (event) => {
      hideLoader(event);
    }
    xhr.onerror = function() {
      window.console.log("Solicitud fallida");
    };
}

const showLoader=(event)=>{
  const loader=document.querySelector('.loader');
  const table=document.querySelector('.generaltable');
  loader.classList.remove('hide');
  loader.classList.add('show');
  table.classList.add('hide');

}

const hideLoader=(event)=>{
  const loader=document.querySelector('.loader');
  const table=document.querySelector('.generaltable');
  loader.classList.remove('show');
  loader.classList.add('hide');
  table.classList.remove('hide');
}

const onLoadFunction=(myXhr)=>{
    if (myXhr.readyState=== 4 && myXhr. status === 200){
        const res=JSON.parse(myXhr.response);
        res[0].assessment_list=res[0].assessment_list.map(obj=>{
          obj.assessment=obj.assessment*1;
          obj.assessment= obj.assessment.toFixed(2);
          obj.attendance=obj.attendance*1;
          obj.attendance= obj.attendance.toFixed(2);
          return obj;
        });
        
        res[0].pages=truncateArrayWithActiveMiddle(res[0].pages,8);
        window.console.log(res[0]);
        showTemplateAssessment(res[0]);
        
    }
}

function showTemplateAssessment(response){
    //Render the choosen mustache template by Javascript
    Templates.renderForPromise('report_coursereport/content-ajax',response)
    .then(({html,js})=>{
    const content=document.querySelector('#content');
    content.innerHTML='';
      Templates.appendNodeContents(content,html,js);
    })
    .catch((error)=>displayException(error));
  }


  function truncateArrayWithActiveMiddle(arr, maxLength) {
    const activeIndex = arr.indexOf(arr.find(item => item.active)); // Combine find and indexOf
  
    // Handle cases where there's no active element or less than maxLength elements
    if (activeIndex === -1 || arr.length <= maxLength) {
      return arr;
    }
  
    // Similar logic to calculate before and after lengths
    const halfLength = Math.floor(maxLength / 2);
    const beforeLength = Math.min(halfLength, activeIndex);
    const afterLength = Math.min(halfLength, arr.length - activeIndex - 1);
  
    // Use a loop to iterate and build the truncated array
    const truncatedArray = [];
    for (let i = activeIndex - beforeLength; i <= activeIndex + afterLength; i++) {
      if (i >= 0 && i < arr.length) { // Ensure we stay within array bounds
        truncatedArray.push(arr[i]);
      }
    }
  
    return truncatedArray;
  }