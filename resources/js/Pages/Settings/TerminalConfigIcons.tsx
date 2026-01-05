import React from 'react'
import { Printer, Settings, FileCheck2, Database, MessageCircleMore, SquareTerminal, ClockArrowUp, DatabaseBackup, DatabaseZap, Table} from 'lucide-react'

export const iconMap: Record<string, React.ReactElement> = {
	'General': <Settings size={18} />,
	'Printer': <Printer  size={18} />,
	'Accreditation': <FileCheck2 size={18} />,
	'Old Data': <Database size={18} />,
	'WeChat Api': <MessageCircleMore size={18} />,
	'Card Terminal': <SquareTerminal size={18} />,
	'Updater': <ClockArrowUp size={18} />,
	'Backup': <DatabaseBackup size={18} />,
	'Zap Integration': <DatabaseZap size={18} />,
	'Terminal Connections': <Table size={18} />,
}
