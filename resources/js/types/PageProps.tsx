export type PageProps = {
	auth: {
		user: {
			userId: number;
			username: string;
			fullName: string;
			domain: string;
			clientGroupId: number;
			clientNetworkId: number;
			softwareId: number;
			permissions: number[];
		} | null;
  	};
	
	clientTerminalDetails: {
		terminalId: string | number;
		clientGroupId: number;
		clientNetworkId: number;
		clientGroupName: string;
		branchId: string;
		branchName: string;
		terminalNo: string;
	}[];
};
