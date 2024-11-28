import React, { useEffect, useState } from 'react';
import axios from 'axios';

interface TableRow {
  id: number;
  queue_id: number;
  user_id: number;
  report_id: number; // Assuming this field is present in the table data
  window_name: string; // Assuming this field is present in the table data
  [key: string]: any;
}

interface TableProps {
  tableName: string;
}

const TableData: React.FC<TableProps> = ({ tableName }) => {
  const [tableData, setTableData] = useState<TableRow[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const [processingItem, setProcessingItem] = useState<TableRow | null>(null);
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDoneDisabled, setIsDoneDisabled] = useState<boolean>(false); // New state for disabling the DONE button
  const [skipMessage, setSkipMessage] = useState<string | null>(null);
  const [timeLeft, setTimeLeft] = useState<number>(180); // 3 minutes = 180 seconds

  // Load table data from the server
  const loadQueue = async () => {
    try {
      const res = await axios.post("http://127.0.0.1:8000/view.table.data", {
        table_name: tableName,
      });
      if (res.data.tableData) {
        setTableData(res.data.tableData);
      } else {
        setError('No data available for the table');
      }
    } catch (err) {
      setError('Error fetching table data');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadQueue();

    const interval = setInterval(() => {
      loadQueue();
    }, 2000); // Poll every 5 seconds

    return () => clearInterval(interval);
  }, [tableName]);

  const handleSkip = async (queueId: number, tableName: string) => {
    setIsProcessing(true);
    try {
      const res = await axios.delete(`http://127.0.0.1:8000/queues/${queueId}/${tableName}`);
      setSkipMessage(res.data.message);
      loadQueue();

      if (processingItem && processingItem.queue_id === queueId) {
        setProcessingItem(null);
      }

      setTimeLeft(180); // Reset timer after skipping
    } catch (err) {
      console.error('Error skipping the queue:', err.response?.data || err.message);
      setSkipMessage('Error skipping the queue.');
    } finally {
      setIsProcessing(false);
    }
  };

  const handleProcessing = (item: TableRow) => {
    setProcessingItem(item);
    setIsProcessing(true);
    setTimeLeft(180); // Reset the timer when processing starts
  };

  const handleDone = async () => {
    if (processingItem) {
      setIsDoneDisabled(true); // Disable DONE button
      try {
        const response = await axios.post('http://127.0.0.1:8000/reports', {
          school_id: processingItem.school_id,
          email: processingItem.email,
          window_name: tableName, 
          arrived_at: processingItem.created_at,
        });
        handleSkip(processingItem.queue_id, tableName);

        setProcessingItem(null);
        setIsProcessing(false);
        setTimeLeft(180); // Reset the timer after processing is done
      } catch (err) {
        console.error('Error sending data to reports:', err.response?.data || err.message);
      } finally {
        setIsDoneDisabled(false); // Re-enable DONE button after processing
        setTimeLeft(180);
      }
    }
  };

  useEffect(() => {
    if (!processingItem) {
      const timer = setInterval(() => {
        setTimeLeft((prevTime) => {
          if (prevTime <= 1) {
            if (tableData.length > 0) {
              handleSkip(tableData[0].queue_id, tableName);
            }
            return 180; // Reset timer after skipping
          }
          return prevTime - 1;
        });
      }, 1000); // Countdown every second

      return () => clearInterval(timer);
    }
  }, [tableData, processingItem]);

  if (loading) {
    return <div>Loading...</div>;
  }

  if (error) {
    return <div>{error}</div>;
  }

  if (!tableData.length) {
    return (
      <>
        <div>   
          <div className="mb-4 p-4 border rounded bg-gray-100">
            <h2 className="font-bold text-red-500">No Queuer at the moment...</h2>
            <p>Queue ID: </p>
            <p>School ID:  </p>
            <p>Email: </p>
            <p>Joined Queue at: </p>
          </div>
        </div>    
        
      </>
    );
  }

  const firstItem = tableData[0];

  return (
    <div className="relative overflow-x-auto">
      <div className="mb-4 p-4 border rounded bg-gray-100">
        <h2 className="font-bold">Processing Item</h2>
        {processingItem ? (
          <div>
            <p>Queue ID: {processingItem.queue_id}</p>
            <p>School ID: {processingItem.school_id} </p>
            <p>Email: {processingItem.email}</p>
            <p>Joined Queue at: {processingItem.created_at}</p>
            {isProcessing && (
              <div className="flex items-center">
                <span className="loader mr-2">
                  <svg className="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                    <path className="opacity-75" fill="currentColor" d="M4 12c0-1.11.19-2.17.54-3.12l1.74.96A8.021 8.021 0 0 0 4 12zm16 0c0 1.11-.19 2.17-.54 3.12l-1.74-.96A8.021 8.021 0 0 0 20 12z"></path>
                  </svg>
                </span>
                <span>Processing...</span>
              </div>
            )}
            <button
              onClick={handleDone}
              className="mt-2 p-2 border rounded bg-green-500 text-white hover:bg-green-600"
              disabled={isDoneDisabled} // Disable DONE button while processing
            >
              DONE
            </button>
          </div>
        ) : firstItem ? (
          <div>
            <p>Queue ID: {firstItem.queue_id}</p>
            <p>Name: {firstItem.school_id} {firstItem.email}</p>
            <p>Time Left: {Math.floor(timeLeft / 60)}:{String(timeLeft % 60).padStart(2, '0')} minutes</p>
            <button
              onClick={() => handleProcessing(firstItem)}
              className="mt-2 p-2 border rounded bg-blue-500 text-white hover:bg-blue-600"
            >
              PROCESS
            </button>
            <button
              onClick={() => handleSkip(firstItem.queue_id, tableName)}
              className="mt-2 p-2 border rounded bg-red-500 text-white hover:bg-red-600"
            >
              SKIP
            </button>
          </div>
        ) : (
          <p>No item being processed.</p>
        )}
      </div>

      {skipMessage && (
        <div className="mb-4 p-2 border rounded bg-green-100 text-green-800">
          {skipMessage}
        </div>
      )}

      <table className="w-full text-sm text-left rtl:text-right text-gray-500">
        <thead className="text-xs text-gray-700 uppercase bg-gray-50">
          <tr>
            {Object.keys(firstItem).filter(key => key !== "updated_at").map((key) => (
              <th key={key} className="px-6 py-4">
                {key === "created_at" ? "Entered queue at" : key}
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {tableData.slice(1).map((row) => (
            <tr key={row.id} className="bg-white border-b">
              {Object.entries(row).filter(([key]) => key !== "updated_at").map(([key, value], index) => (
                <td className="px-6 py-4" key={index}>
                  {value}
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default TableData;
