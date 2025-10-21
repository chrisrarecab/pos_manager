
import type { ReactNode, ElementType } from "react";

/**
 * MODAL PROPS
 */
export type ModalProps = {
	isModalOpen: boolean;
	onClose: () => void;
	title?: string;
	children?: ReactNode;
	data?: any | null;
	footer?: ReactNode;
	sizeClass?: string;
	Icon?: ElementType | undefined;
};

/**
 * MULTI STEP FORM
 */
export type MultiStepFormProps = {
	stepLabels: string[];
	progressBarOffset?: string;
	nextDisabled?: boolean;
	endButtonName?: string;
	onFinished?: () => void;
	onStepNext?: () => void;
	onStepBack?: () => void;
	onReset?: () => void;
	children?: React.ReactNode[];
};

export type MultiStepFormRef = {
	resetSteps: () => void;
	disableNextStep: (val?: boolean) => void;
	getCurrentStep: () => number;
	getDisableStatus: () => boolean;
};
