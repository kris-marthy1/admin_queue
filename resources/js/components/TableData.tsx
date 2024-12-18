import React, { useEffect, useState, useCallback, useRef } from 'react';
import axios from 'axios';

interface TableRow {
  [key: string]: any;
}

interface TableProps {
  tableName: string;
}

const TableData = ({ tableName }: TableProps) => {
  const [tableData, setTableData] = useState<TableRow[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const [processingItem, setProcessingItem] = useState<TableRow | null>(null);
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [isDoneDisabled, setIsDoneDisabled] = useState<boolean>(false);
  const [skipMessage, setSkipMessage] = useState<string | null>(null);
  const [timeLeft, setTimeLeft] = useState<number>(180);
  const [columns, setColumns] = useState<string[]>([]);
  
  // Add lastUpdateTime to track when the data was last modified
  const [lastUpdateTime, setLastUpdateTime] = useState<string | null>(null);
  const pollingInterval = useRef<number | null>(null);
  const forceRefresh = useRef<boolean>(false);
  // Enhanced loadQueue function with timestamp checking
  const loadQueue = useCallback(async () => {
    try {
      const res = await axios.post("http://127.0.0.1:8000/view.table.data", {
        table_name: tableName,
        last_update: forceRefresh.current ? null : lastUpdateTime // Ignore lastUpdateTime when forcing refresh
      });

      if (res.data.tableData && res.data.tableData.length > 0) {
        const newTableData = res.data.tableData;
        
        // Get the most recent update time from the data
        const mostRecentUpdate = newTableData.reduce((latest: string, row: TableRow) => {
          const rowUpdate = row.updated_at || row.created_at;
          return !latest || rowUpdate > latest ? rowUpdate : latest;
        }, '');

        // Update if we have new data or forcing refresh
        if (forceRefresh.current || mostRecentUpdate !== lastUpdateTime) {
          setTableData(newTableData);
          setLastUpdateTime(mostRecentUpdate);

          const columnNames = Object.keys(newTableData[0]).filter(
            key => key !== "updated_at"
          );
          setColumns(columnNames);
          
          // If processing item is not in new data, clear it
          if (processingItem && !newTableData.some(row => row.queue_id === processingItem.queue_id)) {
            setProcessingItem(null);
            setIsProcessing(false);
          }
        }
      } else if (res.data.tableData && res.data.tableData.length === 0) {
        setTableData([]);
        setLastUpdateTime(null);
        setProcessingItem(null);
        setIsProcessing(false);
      }
      
      // Reset force refresh flag
      forceRefresh.current = false;
    } catch (err) {
      console.error('Error fetching table data:', err);
      setError('Error fetching table data');
    } finally {
      setLoading(false);
    }
  }, [tableName, lastUpdateTime, processingItem])

  useEffect(() => {
    const startPolling = () => {
      // Clear existing interval if any
      if (pollingInterval.current) {
        window.clearInterval(pollingInterval.current);
      }
  
      // If not processing, poll more frequently
      const interval = !processingItem ? 500 : 2000; // 0.5s when not processing, 2s when processing
      pollingInterval.current = window.setInterval(loadQueue, interval);
    };
  
    // Initial load
    loadQueue();
    startPolling();
  
    return () => {
      if (pollingInterval.current) {
        window.clearInterval(pollingInterval.current);
      }
    };
  }, [loadQueue, processingItem]);

  const handleSkip = async (queueId: number, tableName: string) => {
    setIsProcessing(true);
    tableName = tableName.toLowerCase();
    try {
      const res = await axios.delete(`http://127.0.0.1:8000/queues`, {
        params: { queueId, tableName },
        validateStatus: (status) => status >= 200 && status < 300,
      });
      setSkipMessage(res.data.message);

      // Clear processing item immediately if it matches
      if (processingItem && processingItem.queue_id === queueId) {
        setProcessingItem(null);
        setIsProcessing(false);
      }
      setTimeLeft(180);
      
      // Force immediate refresh
      forceRefresh.current = true;
      await loadQueue();
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
    setTimeLeft(100000);
  };

  const handleDone = async () => {
    if (processingItem) {
      setIsDoneDisabled(true);
      try {
        await axios.post('http://127.0.0.1:8000/reports', {
          arrived_at: processingItem.created_at,
          queue_id: processingItem.queue_id.toString(),
          window_name: tableName,
        });

        // Clear processing item immediately
        const queueId = processingItem.queue_id;
        setProcessingItem(null);
        setIsProcessing(false);
        setTimeLeft(180);

        // Handle skip after clearing state
        await handleSkip(queueId, tableName);
      } catch (err) {
        console.error('Error sending data to reports:', err);
      } finally {
        setIsDoneDisabled(false);
      }
    }
  };
  useEffect(() => {
    if (!processingItem && tableData.length > 0) {
      const timer = setInterval(() => {
        setTimeLeft((prevTime) => {
          if (prevTime <= 1) {
            handleSkip(tableData[0].queue_id, tableName);
            return 180;
          }
          return prevTime - 1;
        });
      }, 1000);

      return () => clearInterval(timer);
    }
  }, [tableData, processingItem, tableName]);

  const renderItemDetails = (item: TableRow) => {
    return (
      <>
        {columns.map((column: string) => (
          <p key={column} className="capitalize">
            {column === "created_at" ? "Arrived in queue at" : column.replace(/_/g, ' ')}: {item[column]}
          </p>
        ))}
      </>
    );
  };

  if (loading) return <div>Loading...</div>;
  if (error) return <div>{error}</div>;

  const firstItem = tableData[0];

  if (!tableData.length) {
    return (
      <div className="mb-4 p-4 border rounded bg-gray-100">

        <div className="mb-4 p-4 border rounded bg-gray-100 text-xl">
        <h2 className="font-bold text-2xl">Processing Queuer</h2>
          <div>
              <div className="flex items-center">
                <span className="loader mr-2">
                  <svg
                    className="animate-spin h-5 w-5 text-blue-600"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                  </svg>
                    <h2 className="font-bold text-red-500">No Queuer at the moment...</h2>
                </span>
              </div>
          </div>
      </div>


      </div>
    );
  }

  return (
    <div className="relative overflow-x-auto">
      {/* Processing Item Section */}
      <div className="mb-4 p-4 border rounded bg-gray-100 text-xl">
        <h2 className="font-bold text-2xl">Processing Queuer</h2>
        {processingItem ? (
          <div>
            {renderItemDetails(processingItem)}
            {isProcessing && (
              <div className="flex items-center">
                <span className="loader mr-2">
                  <svg
                    className="animate-spin h-5 w-5 text-blue-600"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle
                      className="opacity-25"
                      cx="12"
                      cy="12"
                      r="10"
                      stroke="currentColor"
                      strokeWidth="4"
                    ></circle>
                    <path
                      className="opacity-75"
                      fill="currentColor"
                      d="M4 12c0-1.11.19-2.17.54-3.12l1.74.96A8.021 8.021 0 0 0 4 12zm16 0c0 1.11-.19 2.17-.54 3.12l-1.74-.96A8.021 8.021 0 0 0 20 12z"
                    ></path>
                  </svg>
                </span>
                <span>Processing...</span>
              </div>
            )}
            <button
              onClick={handleDone}
              className="mt-2 p-2 border rounded bg-green-500 text-white hover:bg-green-600"
              disabled={isDoneDisabled}
            >
              DONE
            </button>
          </div>
        ) : firstItem ? (
          <div>
            {renderItemDetails(firstItem)}
            <p>
              Time Left: {Math.floor(timeLeft / 60)}:
              {String(timeLeft % 60).padStart(2, '0')} minutes
            </p>
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

      {/* Table for Next in Line Queuers */}
      <table className="table-auto w-full border-collapse border border-gray-200 mt-4">
        <thead>
          <tr>
            {columns.map((column) => (
              <th
                key={column}
                className="border border-gray-300 px-4 py-2 capitalize bg-gray-100"
              >
                {column === "created_at"
                  ? "Joined queue at"
                  : column.replace(/_/g, ' ')}
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {tableData.slice(1).map((row, index) => (
            <tr key={index} className="hover:bg-gray-50 text-center">
              {columns.map((column) => (
                <td key={column} className="border border-gray-300 px-4 py-2">
                  {row[column]}
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