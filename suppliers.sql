CREATE TABLE suppliers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  address VARCHAR(255) NULL,
  email VARCHAR(190) NULL,
  tax_no VARCHAR(50) NULL,
  notes TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by INT NULL,
  UNIQUE KEY uq_suppliers_name (name),
  KEY idx_suppliers_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE supplier_contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  supplier_id INT NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  role VARCHAR(80) NOT NULL DEFAULT 'Satın Alma Görevlisi',
  phone VARCHAR(40) NULL,
  email VARCHAR(190) NULL,
  notes VARCHAR(255) NULL,
  is_primary TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_contacts_supplier (supplier_id),
  KEY idx_contacts_active (is_active),
  CONSTRAINT fk_contacts_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
