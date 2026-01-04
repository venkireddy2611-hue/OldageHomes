// Initialize EmailJS
(function() {
    emailjs.init("service_wa93zl1");
})();

// Send Contact Form
function sendContactEmail(formData) {
    return emailjs.send("service_wa93zl1", "template_v5qva0j", formData);
}

// Send Admission Form
function sendAdmissionEmail(formData) {
    return emailjs.send("service_wa93zl1", "template_v5qva0j", {
        to_email: "venkireddy9320@gmail.com",
        subject: "New Admission Request",
        patient_name: formData.patientName,
        age: formData.patientAge,
        phone: formData.contactPhone,
        email: formData.contactEmail,
        message: `
Patient: ${formData.patientName}
Age: ${formData.patientAge}
Gender: ${formData.patientGender}
Contact: ${formData.contactName}
Phone: ${formData.contactPhone}
Email: ${formData.contactEmail}
Admission Date: ${formData.admissionDate}
Medical: ${formData.medicalCondition}
        `
    });
}