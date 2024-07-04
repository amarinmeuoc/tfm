
export const init=(XLSX, filesaver,blobutil)=>{
    const boexportAss=document.querySelector('#id_export_ass');
    const boexportAtt=document.querySelector('#id_export_att');
    const boexportTrainee=document.querySelector('#id_export_trainee');

    boexportAss.addEventListener('click',(e)=>{
        exportToExcel(e,XLSX,filesaver,blobutil,'coursereport');
    });

    boexportAtt.addEventListener('click',(e)=>{
        exportToExcel(e,XLSX,filesaver,blobutil,'dailyattendancereport');
    });

    boexportTrainee.addEventListener('click',(e)=>{
        exportToExcel(e,XLSX,filesaver,blobutil,'traineereport');
    });
}

const exportToExcel=(e,XLSX,filesaver,blobutil,op)=>{
    const token=document.querySelector('input[name="token"]').value;
    
    prepareDataToSend(token,op);
}

const prepareDataToSend=(token,op)=>{
    const xhr=new XMLHttpRequest();
    const url='http://'+window.location.hostname+'/webservice/rest/server.php';
    xhr.open('POST',url,true);
 
    const formData= new FormData();
    formData.append('wstoken',token);
    switch (op) {
        case 'coursereport':
            formData.append('wsfunction','report_coursereportadmin_get_total_assessment');
            break;
        case 'dailyattendancereport':
            formData.append('wsfunction','report_coursereportadmin_get_total_dailyattendance');
            break;
        case 'traineereport':
            formData.append('wsfunction','report_coursereportadmin_get_total_trainee_report');
            break;
        default:
            break;
    }
    
    formData.append('moodlewsrestformat','json');
    formData.append('params[0][request]',op);
    
   
    xhr.send(formData);
    xhr.onload=(event)=>{
        onLoadFunction(xhr,op);
    }

    xhr.onprogress = (event)=>{
        onProgressFunction(event);
    } 
    xhr.onerror = function() {
        window.console.log("Solicitud fallida");
    };

}

const onLoadFunction=(myXhr,op)=>{
    const loader=document.querySelector('.loader');
    loader.classList.add('.hide');
    loader.classList.remove('.show');

    if (myXhr.readyState===4 && myXhr.status===200){
        const res=JSON.parse(myXhr.response);
        switch (op) {
            case 'coursereport':
                createExcelFromJSON(res,'courseReport');
                break;
            case 'dailyattendancereport':
                createExcelFromJSON(res,'dailyAttendanceReport');
                break;
            case 'traineereport':
                createExcelFromJSON(res,'traineeReport');
                break;
            default:
                break;
        }
        
        
    }
}

const onProgressFunction=(event) =>{
    console.log(`Uploaded ${event.loaded} of ${event.total}`);
    const loader=document.querySelector('.loader');
    loader.classList.remove('.hide');
    loader.classList.add('.show');
}

const createExcelFromJSON=(res,op)=>{
    
    let listado=[];

    const wb=XLSX.utils.book_new();
    const dr=new Date();
    const dateFile=dr.getDate();
    const month=dr.getMonth()+1
    const year=dr.getFullYear();
    const min=dr.getMinutes();
    const hour=dr.getHours();
    
    wb.Props={
        Title: "Course report",
        Subject: "Training program report",
        Author: "Alberto Marín",
        CreateDate: new Date(year,month,dateFile)
    };

    if (op==='courseReport'){
        if (res[0].assessment_list.length>0){
            listado=res[0].assessment_list.map(elem=>{
                const sdate=new Date(elem.startdate*1000);
                const syear=sdate.getFullYear();
                const smonth=sdate.getMonth()+1;
                const sday=sdate.getDate()+1;
                elem.startdate=new Date(syear+"-"+smonth+"-"+sday);
                const edate=new Date(elem.enddate*1000);
                const eyear=edate.getFullYear();
                const emonth=edate.getMonth()+1;
                const eday=edate.getDate()+1;
                elem.enddate=new Date(eyear+"-"+emonth+"-"+eday);
                elem.assessment=elem.assessment*1;
                elem.assessment=parseFloat(elem.assessment);
                elem.assessment= Math.round(elem.assessment*100)/100;
                elem.attendance=elem.attendance*1;
                elem.attendance=parseFloat(elem.attendance);
                elem.attendance= Math.round(elem.attendance*100)/100;
                delete elem.customerid;
                delete elem.groupid;
    
                return Object.values(elem);
            });
            if (res[0].ifobserver){
                listado=res[0].assessment_list.map(elem=>{
                    delete elem.customercode;
                    return Object.values(elem);
                });
            }
            let titles=Object.keys(res[0].assessment_list[0]);
            listado.unshift(titles);
        }
        wb.SheetNames.push("courseAssessment");
        const ws=XLSX.utils.aoa_to_sheet(listado);
        wb.Sheets["courseAssessment"]=ws;
    }

    if (op==='traineeReport'){
        if (res[0].assessment_list.length>0){
            const data=res[0].assessment_list;
            const groupedByMail=data.reduce((acc,curr)=>{
                const {customercode,groupname,billid,firstname,lastname,email,assessment,attendance}=curr;
                if (!acc[email]){
                    acc[email]={customercode,groupname,billid,firstname,lastname,email,count:0, sumAssessment:0,sumAttendance:0};
                }
                acc[email].count++;
                acc[email].sumAssessment+=parseFloat(assessment)*1;
                acc[email].sumAttendance+=parseFloat(attendance)*1;
                return acc;
            },{});
            const resultado=Object.entries(groupedByMail).map(([email,{customercode,groupname,billid,firstname,lastname,count,sumAssessment,sumAttendance}])=>({
               
               customercode:customercode,
               groupname:groupname,
               billid:billid,
               firstname:firstname,
               lastname:lastname,
               email,
               attendance:sumAttendance/count, 
               assessment:sumAssessment/count,
            }));
            
            listado=resultado.map(elem=>{
                
                elem.assessment=elem.assessment*1;
                elem.assessment=parseFloat(elem.assessment);
                elem.assessment= Math.round(elem.assessment*100)/100;
                elem.attendance=elem.attendance*1;
                elem.attendance=parseFloat(elem.attendance);
                elem.attendance= Math.round(elem.attendance*100)/100;
                delete elem.customerid;
                delete elem.groupid;
                
    
                return Object.values(elem);
            });
            
            //let titles=Object.keys(res[0].assessment_list[0]);
            listado.unshift(['customercode','group','billid','firstname','lastname','email','attendance','assessment']);
        }
        wb.SheetNames.push("traineeReport");
        const ws=XLSX.utils.aoa_to_sheet(listado);
        wb.Sheets["traineeReport"]=ws;
    }

    if (op==='dailyAttendanceReport'){
        if (res[0].attendance_list.length>0){
            listado=res[0].attendance_list.map(elem=>{
                const date=new Date(elem.dateatt*1000);
                const year=date.getFullYear();
                const month=date.getMonth()+1;
                const day=date.getDate()+1;
                elem.dateatt=new Date(year+"-"+month+"-"+day);
                return Object.values(elem);
            });
            let titles=Object.keys(res[0].attendance_list[0]);
            listado.unshift(titles);
        }
        wb.SheetNames.push("AttendanceReport");
        const ws=XLSX.utils.aoa_to_sheet(listado);
        wb.Sheets["AttendanceReport"]=ws;
    }
    
    
    
    
    
    
    const wbout=XLSX.write(wb,{bookType:'xlsx',type:'binary'});
    const nameFile=op+dateFile+'.'+month+'.'+year+'.'+hour+'.'+min+'.xlsx';
    saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}),nameFile)
    
}

const s2ab=(s) => {
    var buf = new ArrayBuffer(s.length);
    var view = new Uint8Array(buf);
    for (var i=0; i!=s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
    return buf;
}