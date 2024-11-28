import React from "react";
import ReactDOM from "react-dom/client";
import TableData from "./TableData";

// Fetch the table name from the data attribute in the div
const tableDataElement = document.getElementById('table-data') as HTMLElement;
const tableName = tableDataElement.getAttribute('data-table-name');

// Mount the TableData component to the root element
const root = ReactDOM.createRoot(tableDataElement);

root.render(
    <React.StrictMode>
        <TableData tableName={tableName || ''} />  {/* Provide a fallback in case tableName is null */}
    </React.StrictMode>
);
