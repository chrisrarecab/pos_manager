import React from 'react';
import Tooltip from '../../Components/Tooltip';
import { EditIcon } from 'lucide-react';

interface TableProps {
	data: Record<string, any>[];
	className?: string;
	maxColumns?: number; 
	onEdit?: (row: any) => void;
}

const Table: React.FC<TableProps> = ({ data, className, maxColumns, onEdit }) => {
	if (!Array.isArray(data) || data.length === 0) {
		return <p className="text-gray-500 text-center">No terminal connections found.</p>;
	}

	const columns = Object.keys(data[0] ?? {});
	const displayedColumns = maxColumns ? columns.slice(0, maxColumns) : columns;

	return (
		<div className={`bg-white border border-gray-200 rounded-md overflow-hidden ${className ?? ''}`}>
		<div className="overflow-x-auto">
			<table className="min-w-full divide-y divide-gray-200">
			<thead className="bg-gray-50">
				<tr>
				{displayedColumns.map(col => (
					<th key={col} className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
					{col}
					</th>
				))}
				<th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
					Actions
				</th>
				</tr>
			</thead>
			<tbody className="bg-white divide-y divide-gray-200">
				{data.map((row, idx) => (
				<tr key={idx}>
					{displayedColumns.map(col => (
					<td key={col} className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
						{row[col] ?? '-'}
					</td>
					))}
					<td className="px-4 py-3 whitespace-nowrap text-sm">
					<button className="text-[var(--color-accent-active)] hover:text-[var(--color-add-hover)] p-1" onClick={() => onEdit?.(row)}>
						<Tooltip content="Edit connection">
						<EditIcon className="h-4 w-4" />
						</Tooltip>
					</button>
					</td>
				</tr>
				))}
			</tbody>
			</table>
		</div>
	

			<div className="flex items-center justify-center px-4 py-3 bg-gray-50 border-t border-gray-200"> 
				<nav className="flex items-center"> 
					<Tooltip content="First page">  <button className="px-2 py-1 mx-1 text-gray-500 hover:text-gray-700"> « </button>  </Tooltip>
					<Tooltip content="Previous page"> <button className="px-2 py-1 mx-1 text-gray-500 hover:text-gray-700"> ‹ </button>  </Tooltip> 
					<Tooltip content="Page 1"> <button className="px-3 py-1 mx-1 bg-[var(--color-accent-active)] text-white rounded"> 1 </button> </Tooltip>
					<Tooltip content="Next page"> <button className="px-2 py-1 mx-1 text-gray-500 hover:text-gray-700"> › </button> </Tooltip> 
					<Tooltip content="Last page"> <button className="px-2 py-1 mx-1 text-gray-500 hover:text-gray-700"> » </button> </Tooltip> 
				</nav>
			</div> 
		</div>
	);
};

export default Table;
