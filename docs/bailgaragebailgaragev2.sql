CREATE TABLE
    Garages (
        id INT AUTO_INCREMENT,
        ownerId INT NOT NULL,
        address VARCHAR(255),
        additionalAddress VARCHAR(255),
        cityId INT NOT NULL,
        country VARCHAR(50),
        garageNumber INT,
        lotNumber INT NOT NULL,
        rentWithoutCharges DECIMAL(15, 2),
        charges DECIMAL(15, 2),
        surface INT,
        reference VARCHAR(255),
        attachmentName VARCHAR(100) NOT NULL, -- Modification ici
        trustee VARCHAR(100),
        caution DECIMAL(15, 2),
        comment TEXT,
        ownerNote TEXT,
        PRIMARY KEY (id)
    );

CREATE TABLE
    users (
        id INT AUTO_INCREMENT,
        name VARCHAR(100),
        firstName VARCHAR(100),
        email VARCHAR(100) UNIQUE,
        password VARCHAR(255),
        roleId INT,
        createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
        updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        resetToken VARCHAR(64),
        PRIMARY KEY (id)
    );

CREATE TABLE
    roles (
        id INT AUTO_INCREMENT,
        name VARCHAR(50) UNIQUE,
        description TEXT,
        PRIMARY KEY (id)
    );

CREATE TABLE
    additionalIban (
        id INT AUTO_INCREMENT,
        ownerId INT NOT NULL,
        iban VARCHAR(34),
        bic VARCHAR(11),
        PRIMARY KEY (id)
    );

CREATE TABLE
    tenants (
        id INT AUTO_INCREMENT,
        name VARCHAR(100),
        firstName VARCHAR(100),
        address VARCHAR(255),
        additionalAddress VARCHAR(255),
        cityId INT,
        phoneNumber VARCHAR(15),
        landlinePhoneNumber VARCHAR(15),
        email VARCHAR(100) UNIQUE,
        rgpd BOOLEAN DEFAULT FALSE,
        attachmentPath VARCHAR(255),
        gender BOOLEAN DEFAULT FALSE,
        receipt BOOLEAN DEFAULT FALSE,
        ownerNote TEXT,
        PRIMARY KEY (id)
    );

CREATE TABLE
    payments (
        id INT AUTO_INCREMENT,
        leaseId INT NOT NULL,
        monthPayment INT,
        status BOOLEAN DEFAULT FALSE,
        amount DECIMAL(15, 2),
        _date DATE,
        methodPayment VARCHAR(50),
        ownerNote TEXT,
        PRIMARY KEY (id)
    );

CREATE TABLE
    owner (
        id INT AUTO_INCREMENT,
        userId INT NOT NULL,
        company VARCHAR(100),
        address VARCHAR(255),
        additionalAddress VARCHAR(255),
        cityId INT,
        email VARCHAR(100) UNIQUE,
        phoneNumber VARCHAR(15),
        iban VARCHAR(34),
        bic VARCHAR(11),
        attachmentPath VARCHAR(255),
        gender BOOLEAN DEFAULT FALSE,
        adminNote TEXT,
        PRIMARY KEY (id)
    );

CREATE TABLE
    leases (
        id INT AUTO_INCREMENT,
        tenantId INT NOT NULL,
        garageId INT NOT NULL,
        madeThe DATE,
        madeIn VARCHAR(100),
        startDate DATE,
        endDate DATE,
        duration INT,
        rentAmount DECIMAL(15, 2),
        rentAmountInLetter VARCHAR(255),
        chargesAmount DECIMAL(15, 2),
        chargesAmountInLetter VARCHAR(255),
        totalAmountMonthly DECIMAL(15, 2),
        totalAmountMonthlyInLetter VARCHAR(255),
        prorata DECIMAL(15, 2),
        prorataInLetter VARCHAR(255),
        endProrata DECIMAL(15, 2),
        caution DECIMAL(15, 2),
        cautionInLetter VARCHAR(255),
        numberOfKey INT,
        numberOfBeep INT,
        status BOOLEAN DEFAULT FALSE,
        attachmentPath VARCHAR(255),
        ownerNote TEXT,
        PRIMARY KEY (id)
    );

CREATE TABLE
    emailTemplate (
        name VARCHAR(100),
        subject VARCHAR(255),
        content TEXT,
        PRIMAR;Y KEY (name)
    );
    
CREATE TABLE Citys (
    insee_code INT PRIMARY KEY,
    city_code VARCHAR(255),
    zip_code VARCHAR(10),
    label VARCHAR(255),
    latitude DECIMAL(10,8),
    longitude DECIMAL(10,8),
    department_name VARCHAR(255),
    department_number VARCHAR(10),
    region_name VARCHAR(255),
    region_geojson_name VARCHAR(255)
);


ALTER TABLE Garages ADD CONSTRAINT fk_garages_owner FOREIGN KEY (ownerId) REFERENCES owner (id);

ALTER TABLE Garages ADD CONSTRAINT fk_garages_city FOREIGN KEY (cityId) REFERENCES citys (id);

ALTER TABLE users ADD CONSTRAINT fk_users_role FOREIGN KEY (roleId) REFERENCES roles (id);

ALTER TABLE additionalIban ADD CONSTRAINT fk_additionalIban_owner FOREIGN KEY (ownerId) REFERENCES owner (id);

ALTER TABLE tenants ADD CONSTRAINT fk_tenants_city FOREIGN KEY (cityId) REFERENCES citys (id);

ALTER TABLE payments ADD CONSTRAINT fk_payments_lease FOREIGN KEY (leaseId) REFERENCES leases (id);

ALTER TABLE owner ADD CONSTRAINT fk_owner_user FOREIGN KEY (userId) REFERENCES users (id);

ALTER TABLE owner ADD CONSTRAINT fk_owner_city FOREIGN KEY (cityId) REFERENCES citys (id);

ALTER TABLE leases ADD CONSTRAINT fk_leases_tenant FOREIGN KEY (tenantId) REFERENCES tenants (id);

ALTER TABLE leases ADD CONSTRAINT fk_leases_garage FOREIGN KEY (garageId) REFERENCES Garages (id);