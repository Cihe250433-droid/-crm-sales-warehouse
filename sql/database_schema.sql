CREATE DATABASE IF NOT EXISTS crm_sales_warehouse;
USE crm_sales_warehouse;

-- ==========================================
-- DROP EXISTING TABLES
-- ==========================================
DROP TABLE IF EXISTS sales_pipeline;
DROP TABLE IF EXISTS sales_teams;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS accounts;

-- ==========================================
-- ACCOUNTS TABLE
-- ==========================================
CREATE TABLE accounts (
    account VARCHAR(255) PRIMARY KEY,
    sector VARCHAR(100),
    year_established INT,
    revenue DECIMAL(15,2),
    employees INT,
    office_location VARCHAR(100),
    subsidiary_of VARCHAR(255)
);

-- ==========================================
-- PRODUCTS TABLE
-- ==========================================
CREATE TABLE products (
    product VARCHAR(255) PRIMARY KEY,
    series VARCHAR(100),
    sales_price DECIMAL(10,2)
);

-- ==========================================
-- SALES TEAMS TABLE
-- ==========================================
CREATE TABLE sales_teams (
    sales_agent VARCHAR(255) PRIMARY KEY,
    manager VARCHAR(255),
    regional_office VARCHAR(100)
);

-- ==========================================
-- SALES PIPELINE TABLE
-- ==========================================
CREATE TABLE sales_pipeline (
    opportunity_id VARCHAR(50) PRIMARY KEY,
    sales_agent VARCHAR(255),
    product VARCHAR(255),
    account VARCHAR(255),
    deal_stage VARCHAR(50),
    engage_date DATE NULL,
    close_date DATE NULL,
    close_value DECIMAL(12,2) NULL,

    INDEX idx_sales_agent (sales_agent),
    INDEX idx_product (product),
    INDEX idx_account (account),
    INDEX idx_deal_stage (deal_stage),

    CONSTRAINT fk_pipeline_sales_agent
        FOREIGN KEY (sales_agent)
        REFERENCES sales_teams (sales_agent)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_pipeline_product
        FOREIGN KEY (product)
        REFERENCES products (product)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_pipeline_account
        FOREIGN KEY (account)
        REFERENCES accounts (account)
        ON UPDATE CASCADE
        ON DELETE SET NULL
);

-- ==========================================
-- ANALYSIS VIEWS (OPTIONAL BUT USEFUL)
-- ==========================================

-- Total annual revenue grouped by establishment year
CREATE OR REPLACE VIEW vw_revenue_by_establishment_year AS
SELECT
    year_established,
    SUM(revenue) AS total_revenue
FROM accounts
GROUP BY year_established
ORDER BY year_established;

-- Average opportunity value by product
CREATE OR REPLACE VIEW vw_average_opportunity_by_product AS
SELECT
    product,
    ROUND(AVG(close_value), 2) AS average_opportunity_value
FROM sales_pipeline
WHERE close_value IS NOT NULL
GROUP BY product
ORDER BY average_opportunity_value DESC;

-- Won and Lost opportunities only
CREATE OR REPLACE VIEW vw_won_lost_opportunities AS
SELECT
    opportunity_id,
    sales_agent,
    product,
    account,
    deal_stage,
    close_date,
    close_value
FROM sales_pipeline
WHERE deal_stage IN ('Won', 'Lost')
ORDER BY close_date DESC;