// printTemplate.js
export const getDocumentTemplate = (type, data, id) => {
    return `
        <!DOCTYPE html>
        <html>
        <head>
            <title>${type === 'sale' ? 'Sales Receipt' : 'Invoice'}</title>
            <link rel="stylesheet" href="printStyles.css">
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <img src="/path/to/your/logo.png" alt="Company Logo" class="logo">
                    <div class="company-name">Your Company Name</div>
                    <div class="document-type">${type === 'sale' ? 'Sales Receipt' : 'Invoice'}</div>
                </div>
                
                <div class="info-section">
                    <div class="info-block">
                        <h3>Bill To:</h3>
                        <p><strong>${data.customer_name}</strong></p>
                        <p>Customer #: ${data.customer_number}</p>
                    </div>
                    <div class="info-block" style="text-align: right;">
                        <h3>${type === 'sale' ? 'Receipt' : 'Invoice'} Details:</h3>
                        <p><strong>Date:</strong> ${formatDate(data.invoice_date)}</p>
                        <p><strong>${type === 'sale' ? 'Receipt' : 'Invoice'} #:</strong> ${id}</p>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Product Description</th>
                            <th style="text-align: center;">Quantity</th>
                            <th style="text-align: right;">Unit Price</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.products.map(product => `
                            <tr>
                                <td>${product.product_name}</td>
                                <td style="text-align: center;">${product.quantity}</td>
                                <td style="text-align: right;">${formatCurrency(product.unit_price)}</td>
                                <td style="text-align: right;">${formatCurrency(product.total_price)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>

                <div class="totals">
                    <table>
                        <tr>
                            <td><strong>Subtotal:</strong></td>
                            <td style="text-align: right;">${formatCurrency(data.products.reduce((sum, product) => sum + product.total_price, 0))}</td>
                        </tr>
                        <tr>
                            <td><strong>Tax (${data.tax_rate || '0'}%):</strong></td>
                            <td style="text-align: right;">${formatCurrency(data.tax_amount || 0)}</td>
                        </tr>
                        <tr>
                            <td><strong>Total:</strong></td>
                            <td style="text-align: right;"><strong>${formatCurrency(data.total_amount || data.products.reduce((sum, product) => sum + product.total_price, 0))}</strong></td>
                        </tr>
                    </table>
                </div>

                <div class="footer">
                    <p>Thank you for your business!</p>
                    <p>Your Company Name<br>
                    123 Business Street, City, State ZIP<br>
                    Phone: (555) 555-5555 | Email: support@yourcompany.com</p>
                </div>
            </div>
        </body>
        </html>
    `;
};

// printStyles.css
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    color: #333;
}
.container {
    max-width: 800px;
    margin: 0 auto;
}
.header {
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #eee;
    padding-bottom: 20px;
}
.logo {
    max-width: 200px;
    margin-bottom: 15px;
}
.company-name {
    font-size: 24px;
    font-weight: bold;
    margin: 10px 0;
}
.document-type {
    font-size: 20px;
    color: #666;
    margin: 5px 0;
}
.info-section {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
}
.info-block {
    flex: 1;
}
.info-block h3 {
    color: #666;
    margin-bottom: 5px;
}
.info-block p {
    margin: 5px 0;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}
th {
    background-color: #f8f8f8;
    padding: 12px;
    text-align: left;
}
td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}
.totals {
    float: right;
    width: 300px;
    margin-top: 20px;
}
.totals table {
    margin: 0;
}
.totals td {
    border: none;
}
.footer {
    margin-top: 50px;
    text-align: center;
    color: #666;
    font-size: 14px;
    border-top: 2px solid #eee;
    padding-top: 20px;
}
@media print {
    body {
        padding: 0;
    }
    .no-print {
        display: none;
    }
}

// utils.js
export const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};

export const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// printDocument.js
import { formatCurrency, formatDate } from './utils.js';
import { getDocumentTemplate } from './printTemplate.js';

export const printDocument = (type, id) => {
    const actionType = type === 'sale' ? 'print_sale' : 'print_invoice';
    const idParam = type === 'sale' ? 'sale_id' : 'invoice_id';

    fetch('api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=${actionType}&${idParam}=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const printWindow = window.open('', '_blank');
            const content = getDocumentTemplate(type, data.data, id);
            
            printWindow.document.write(content);
            printWindow.document.close();
            printWindow.print();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(`An error occurred while printing the ${type}.`);
    });
};