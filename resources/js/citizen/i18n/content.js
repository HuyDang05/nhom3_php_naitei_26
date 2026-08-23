const CATEGORY_NAMES_EN = {
    ADMINISTRATION: 'Civil Status, Administration & Certification',
    EDUCATION: 'Education & Training',
    HEALTHCARE: 'Healthcare',
    CONSTRUCTION: 'Construction',
    NATURAL_RESOURCES: 'Land & Natural Resources',
    LABOR_EMPLOYMENT: 'Labor & Employment',
    SOCIAL_WELFARE: 'Social Welfare',
    MERITORIOUS_SERVICES: 'People with Meritorious Services',
    URBAN_PLANNING: 'Urban Planning & Architecture',
    ENVIRONMENT: 'Environment',
    BUSINESS_REGISTRATION: 'Business Registration',
    INVESTMENT_SUPPORT: 'Investment & Business Support',
    CULTURE_SPORTS_TOURISM: 'Culture, Sports & Tourism',
    INFORMATION_COMMUNICATIONS: 'Information & Communications',
    EDUCATION_SUPPORT: 'Tuition Support Policies',
};

const CATEGORY_NAME_LOOKUP_EN = {
    'Hành chính, hộ tịch và chứng thực': CATEGORY_NAMES_EN.ADMINISTRATION,
    'Giáo dục và đào tạo': CATEGORY_NAMES_EN.EDUCATION,
    'Y tế và chăm sóc sức khỏe': CATEGORY_NAMES_EN.HEALTHCARE,
    'Xây dựng': CATEGORY_NAMES_EN.CONSTRUCTION,
    'Đất đai và tài nguyên': CATEGORY_NAMES_EN.NATURAL_RESOURCES,
    'Lao động và việc làm': CATEGORY_NAMES_EN.LABOR_EMPLOYMENT,
    'Bảo trợ xã hội': CATEGORY_NAMES_EN.SOCIAL_WELFARE,
    'Người có công': CATEGORY_NAMES_EN.MERITORIOUS_SERVICES,
    'Quy hoạch và kiến trúc': CATEGORY_NAMES_EN.URBAN_PLANNING,
    'Môi trường': CATEGORY_NAMES_EN.ENVIRONMENT,
    'Đăng ký kinh doanh': CATEGORY_NAMES_EN.BUSINESS_REGISTRATION,
    'Đầu tư và hỗ trợ doanh nghiệp': CATEGORY_NAMES_EN.INVESTMENT_SUPPORT,
    'Văn hóa, thể thao và du lịch': CATEGORY_NAMES_EN.CULTURE_SPORTS_TOURISM,
    'Thông tin và truyền thông': CATEGORY_NAMES_EN.INFORMATION_COMMUNICATIONS,
    'Chính sách học phí': CATEGORY_NAMES_EN.EDUCATION_SUPPORT,
};

const SERVICE_CONTENT_EN = {
    CIVIL_STATUS_CERTIFICATE: {
        name: 'Civil Status Certificate',
        description: 'Issue a certificate of civil status information based on registered records.',
        requirements: 'A valid citizen identity document is required.',
        documents: { citizen_id_copy: 'Citizen ID copy' },
    },
    CONSTRUCTION_PERMIT: {
        name: 'Construction Permit',
        description: 'Receive and assess applications for construction permits.',
        requirements: 'The project must comply with planning and current construction regulations.',
        fields: { construction_area: 'Construction area' },
        documents: { design_drawing: 'Design drawing' },
    },
    PUBLIC_SCHOOL_ENROLLMENT: {
        name: 'Public School Enrollment',
        description: 'Register a student for enrollment at a public school online.',
        requirements: 'The student must meet the applicable eligibility and enrollment-area requirements.',
        documents: {
            birth_certificate: 'Birth certificate copy',
            residence_confirmation: 'Residence confirmation',
        },
    },
    HEALTHCARE_SUPPORT_REGISTRATION: {
        name: 'Healthcare Support Registration',
        description: 'Apply for healthcare support available to eligible applicants.',
        requirements: 'The applicant must belong to an eligible support group under current regulations.',
        documents: {
            citizen_id_copy: 'Citizen ID copy',
            eligibility_document: 'Eligibility evidence',
        },
    },
    LAND_RECORD_INFORMATION: {
        name: 'Land Record Information Request',
        description: 'Request archived information about a land lot and its land-use rights.',
        requirements: 'The land lot and purpose of the information request must be clearly identified.',
        fields: { land_lot_number: 'Land lot number' },
        documents: { citizen_id_copy: 'Citizen ID copy' },
    },
    FOOD_SAFETY_CERTIFICATE: {
        name: 'Food Safety Eligibility Certificate',
        description: 'Assess and certify food safety eligibility for food service establishments.',
    },
    SPORTS_BUSINESS_CERTIFICATE: {
        name: 'Sports Business Eligibility Certificate',
        description: 'Assess the facilities, equipment, and professional staff of sports businesses.',
    },
    ENVIRONMENTAL_PERMIT: {
        name: 'Environmental Permit',
        description: 'Assess and issue environmental permits for authorized projects and facilities with discharge activities.',
    },
    GENERAL_WEBSITE_LICENSE: {
        name: 'General Information Website License',
        description: 'Assess and license general information websites that provide information online.',
    },
    PUBLIC_PERFORMANCE_LICENSE: {
        name: 'Public Performance License',
        description: 'Assess content and venues, then license artistic performances for the public.',
    },
    NON_COMMERCIAL_PUBLICATION_LICENSE: {
        name: 'Non-commercial Publication License',
        description: 'License non-commercial publications used for communication, professional guidance, or public duties.',
    },
    FOREIGN_WORK_PERMIT_REISSUE: {
        name: 'Foreign Worker Permit Reissuance',
        description: 'Reissue a valid work permit that was lost, damaged, or requires an approved information update.',
    },
    FIRST_LAND_USE_CERTIFICATE: {
        name: 'First-time Land Use Rights Certificate',
        description: 'Register and issue the first certificate of land-use rights and ownership of land-attached assets.',
    },
    HOUSE_REPAIR_PERMIT: {
        name: 'House Repair and Renovation Permit',
        description: 'Assess home repair or renovation applications involving structural, functional, or exterior changes.',
    },
    SITE_PLAN_APPROVAL: {
        name: 'Site Plan Approval',
        description: 'Assess and approve a project site plan against the approved planning framework.',
    },
    CERTIFIED_COPY_FROM_ORIGINAL: {
        name: 'Certified Copy from an Original',
        description: 'Certify that a copy matches an original document issued by a competent authority.',
    },
    PLANNING_INFORMATION_REQUEST: {
        name: 'Construction Planning Information Request',
        description: 'Provide building lines, land-use functions, and planning indicators for a requested location.',
    },
    INVESTMENT_PROJECT_REGISTRATION: {
        name: 'Domestic Investment Project Registration',
        description: 'Receive information and registration documents for investment projects under local authority.',
    },
    ENVIRONMENTAL_REGISTRATION: {
        name: 'Environmental Registration',
        description: 'Receive environmental registrations from investment projects or production facilities subject to registration.',
    },
    SME_SUPPORT_REGISTRATION: {
        name: 'SME Support Program Registration',
        description: 'Register an SME for consulting, training, digital transformation, or market development support.',
    },
    HOUSEHOLD_BUSINESS_REGISTRATION: {
        name: 'Household Business Registration',
        description: 'Issue a household business registration certificate to an individual or household members.',
    },
    HOUSEHOLD_BUSINESS_CHANGE: {
        name: 'Household Business Registration Update',
        description: 'Update the registered name, address, business lines, capital, or owner information of a household business.',
    },
    SCHOOL_TRANSFER_REQUEST: {
        name: 'Student School Transfer Request',
        description: 'Receive requests to transfer students between general education institutions under the proper authority.',
    },
    MERITORIOUS_MONTHLY_ALLOWANCE: {
        name: 'Monthly Preferential Allowance Application',
        description: 'Receive applications for monthly preferential allowances for eligible people or their relatives.',
    },
    LEARNING_COST_SUPPORT: {
        name: 'Learning Cost Support Application',
        description: 'Provide learning cost support for eligible children and students under current regulations.',
    },
    SOCIAL_FUNERAL_SUPPORT: {
        name: 'Social Funeral Expense Support',
        description: 'Provide funeral expense support for eligible social welfare beneficiaries.',
    },
    JOB_PLACEMENT_SUPPORT: {
        name: 'Job Placement Support Request',
        description: 'Receive job-seeking needs and connect workers with suitable employers.',
    },
    REGULAR_SOCIAL_ASSISTANCE: {
        name: 'Monthly Social Assistance Application',
        description: 'Provide monthly social assistance to eligible social welfare beneficiaries.',
    },
    TUITION_EXEMPTION_REDUCTION: {
        name: 'Tuition Exemption or Reduction Application',
        description: 'Provide tuition exemptions or reductions to learners who qualify under current policies.',
    },
    MARTYR_RELATIVE_CONFIRMATION: {
        name: "Martyr's Relative Confirmation",
        description: 'Confirm a family relationship with a martyr for access to applicable preferential benefits.',
    },
};

function localizeItems(items, labels = {}) {
    if (!Array.isArray(items)) {
        return items;
    }

    return items.map((item) => {
        if (!item || typeof item === 'string') {
            return item;
        }

        return {
            ...item,
            label: labels[item.code ?? item.name] ?? item.label,
        };
    });
}

function localizeCategoryName(service) {
    return CATEGORY_NAMES_EN[service.category_code]
        ?? CATEGORY_NAME_LOOKUP_EN[service.category_name]
        ?? service.category_name;
}

export function localizeCategory(category, language) {
    if (language !== 'en' || !category) {
        return category;
    }

    return {
        ...category,
        name: CATEGORY_NAMES_EN[category.code] ?? CATEGORY_NAME_LOOKUP_EN[category.name] ?? category.name,
    };
}

export function localizeService(service, language) {
    if (language !== 'en' || !service) {
        return service;
    }

    const localized = SERVICE_CONTENT_EN[service.code];

    if (!localized) {
        return {
            ...service,
            category_name: localizeCategoryName(service),
        };
    }

    return {
        ...service,
        name: localized.name,
        description: localized.description,
        requirements: localized.requirements ?? service.requirements,
        category_name: localizeCategoryName(service),
        form_schema: localizeItems(service.form_schema, localized.fields),
        document_requirements: localizeItems(service.document_requirements, localized.documents),
    };
}
