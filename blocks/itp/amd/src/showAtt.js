import {exception as displayException} from 'core/notification';
import Templates from 'core/templates';
export const init = (token,email,firstname,lastname) => {
    let linkatt=document.querySelectorAll('.linkatt');
    if (linkatt.length>0 && linkatt!==null){
      customer=linkatt[0].attributes.customer.value;
      group=linkatt[0].attributes.group.value;
      billid=linkatt[0].attributes.billid.value;
      startdate=linkatt[0].attributes.startdate.value;
      enddate=linkatt[0].attributes.enddate.value;

      linkatt.forEach((link)=>{
        link.addEventListener('click',(event)=>{
          showAttendanceList(event,token,email,firstname,lastname);
        });
      });
    } else  {
      customer=null;
      group=null;
      billid=null;
      startdate=null;
      enddate=null;
    }

    
};

function showTemplateAssessment(response){
  Templates.renderForPromise('block_itp/attendanceDetails',response)
  .then(({html,js})=>{
    const content=document.querySelector('.block_itp_content');
    content.innerHTML='';
    Templates.appendNodeContents('.block_itp_content',html,js);
  })
  .catch((error)=>displayException(error));
}

let showAttendanceList=(e,token,email,firstname,lastname)=>{
  e.preventDefault();
  let courseid=e.target.attributes.courseid.value;
  const xhr=new XMLHttpRequest();
  const url=window.location.protocol+'//'+window.location.hostname+'/webservice/rest/server.php';
  xhr.open('POST',url,true);
  const formData= new FormData();
  formData.append('wstoken',token);
  formData.append('wsfunction','block_itp_get_daily_attendance');
  formData.append('moodlewsrestformat','json');
  formData.append('params[0][courseid]',courseid);
  formData.append('params[0][email]',email);
  formData.append('params[0][startdate]',startdate);
  formData.append('params[0][enddate]',enddate);

  xhr.send(formData);
    xhr.onload = (event) => {
      onLoadFunction(xhr,firstname,lastname,token);
    };
    xhr.onprogress = (event)=>{
      onProgressFunction(event);
    };
    
    xhr.onerror = function() {
      window.console.log("Solicitud fallida");
    };
};

const onProgressFunction=(event)=> {
  if (event.lengthComputable) {
    window.console.log(`Recibidos ${event.loaded} de ${event.total} bytes`);
  } else {
    window.console.log(`Recibidos ${event.loaded} bytes`); // sin Content-Length
  }
};

const onLoadFunction=(myxhr,firstname,lastname,token)=>{
  if (myxhr.readyState=== 4 && myxhr.status === 200){
    const res=JSON.parse(myxhr.response);
    const formattedData = {
      token:token,
      customer:customer,
      group:group,
      billid:billid,
      startdate:startdate,
      enddate:enddate,
      message:(res.length===0)?'No Absent or Late for this period of time. Please check another period or contact to the administrator':'',
      user: {
        firstname:firstname,
        lastname:lastname
      },
      dailyAttendance: getAttendanceResults(res)
    }
    //There are no records
    if (res.length===0){
        formattedData.message="Waiting for attendance to be uploaded. Please, wait until attendance is uploaded."
    } else {
      const listofPresents=res.filter(elem=>{
                                  return elem.description==='Present';
                              });
      if (listofPresents.length===res.length){
        formattedData.message="The attendance for this course is 100%";
      } 
    
    }
    
    showTemplateAssessment(formattedData);

  }
}

const getAttendanceResults=(res)=>{
  return res.filter(item => {
    // Customize object properties for the template as needed
    const pattern=/Present/i;
    return !pattern.test(item.description)
  }).map(item=>{
    return{
      // Example:
      courseid: item.courseid,
      coursename: item.coursename,
      shortname:item.shortname,
      date:item.date,
      firstname:item.firstname,
      lastname:item.lastname,
      description:item.description,
      feedback:item.feedback,
      // ... add other properties
    };
  })
}