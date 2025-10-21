export type TabType = {
	id: number;
	name: string;
	order: number;
	tip: string;
	created_by: number;
	created_date: string;          
	last_modified_by: number;
	last_modified_date: string;     
	is_deleted: number;          
}

export interface SettingType {
  id: number;
  name: string;
  description: string;
  tip: string | null;

  form_element: "radio_button" | "checkbox" | "select" | "input" | string;
  options: SettingOption[];

  value: number | string | null;

  setting_tab_id: number;
  software_id: number;

  type: string; // e.g., "admin_only|shared"
  is_deleted: number;

  created_by: number;
  created_date: string;
  last_modified_by: string;
  last_modified_date: string;

  issues: IssueType[]; 
  terminal_setting: TerminalSettingType | null;
  user: UserType; 
}

type SettingOption = {
  id: number;
  setting_id: number;
  name: string;
  value: string | number;
};

export interface IssueType {
  id: number;
  value: string;

}

export interface UserType {
  id: number;
  full_name: string;
}

export interface TerminalSettingType {
  id: number;
  core_terminal_id: number;
  cirms_terminal_id: string;
  setting_id: number;
  value: string;

}

export interface TableProps {
  data: any[];
  className: string;
}