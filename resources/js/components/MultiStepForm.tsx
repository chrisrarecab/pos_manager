import React, { useState, useMemo, useImperativeHandle, forwardRef } from "react";
import type { MultiStepFormProps, MultiStepFormRef } from "@/types/CommonProps";


const MultiStepForm = forwardRef<MultiStepFormRef, MultiStepFormProps>(
	(
		{
			stepLabels,
			progressBarOffset = "15%",
			nextDisabled = false,
			endButtonName = "Finish",
			onFinished,
			onStepNext,
			onStepBack,
			onReset,
			children,
		},
		ref
	) => {
		const [step, setStep] = useState(1);
		const [isDisabled, setIsDisabled] = useState(nextDisabled);

		const isFirstStep = step === 1;
		const isLastStep = step === stepLabels.length;

		const nextStep = () => {
			if (step < stepLabels.length) {
				const newStep = step + 1;
				setStep(newStep);
				setIsDisabled(nextDisabled);

				if (newStep === stepLabels.length) {
					setIsDisabled(true);
					onFinished?.();
				} 
				else {
					onStepNext?.();
				}
			}
		};

		const prevStep = () => {
			if (step > 1) {
				const newStep = step - 1;
				setStep(newStep);
				setIsDisabled(false);
				onStepBack?.();
			}
		};

		const resetSteps = () => {
			setStep(1);
			setIsDisabled(nextDisabled);
			onReset?.();
		};

		const disableNextStep = (val: boolean = true) => {
			setIsDisabled(val);
		};

		const getCurrentStep = () => step;
		const getDisableStatus = () => isDisabled;

		useImperativeHandle(ref, () => ({
			resetSteps,
			disableNextStep,
			getCurrentStep,
			getDisableStatus,
		}));

		const progressStyle = useMemo(() => {
			if (stepLabels.length <= 1) return { width: "0%" };
			const progressPercentage = ((step - 1) / (stepLabels.length - 1)) * 100;

			return { width: `${progressPercentage}%` };
		}, [step, stepLabels]);

		const progressBarStyle = useMemo(() => ({
				left: progressBarOffset,
				right: progressBarOffset,
			}),
			[progressBarOffset]
		);

    	return (
			<div className="max-w-[1500px] mx-auto p-8 font-sans">
				{/* Progress Tracker */}
				<div className="relative mb-10">
					<div className="flex justify-between relative z-10">
						{stepLabels.map((label, index) => (
						<div key={index}
							className={`flex flex-col items-center flex-1 relative z-20 ${
							step === index + 1
								? "active"
								: step > index + 1
								? "completed"
								: ""
							}`}
						>
							<div aria-current={step === index + 1 ? "step" : undefined} 
								aria-label={`Step ${index + 1}: ${label}`}
								className="w-10 h-10 flex items-center justify-center rounded-full font-bold transition-all border-4"
								style={{
									// Set the background color based on the step status
									backgroundColor:
										step === index + 1
										? "#fff"    // Current step 
										: step > index + 1
										? "var(--color-accent-active)" // Completed steps 
										: "#fff",   // Future steps

									// Set the text color based on the step status
									color:
										step === index + 1
										? "var(--color-accent-active)"  // Current step 
										: step > index + 1
										? "#fff" // Completed steps
										: "var(--color-primary-light)", // Future steps

									// Set the border color based on the step status
									borderColor:
										step === index + 1
										? "var(--color-accent-active)" // Current step
										: step > index + 1
										? "var(--color-accent-active)"  // Completed steps 
										: "var(--color-primary-light)", // Future steps
									}}
							>
								{index + 1}
							</div>
							<div className="mt-2 text-center text-sm transition-all font-semibold"
								style={{
									color: step === index + 1
										? "var(--color-accent-active)"  // current step label
										: step > index + 1
										? "var(--color-add-hover)"  // completed steps
										: "#6b7280",  // future steps, equivalent to text-gray-500
									}}
							>
								{label}
							</div>

						</div>
						))}
					</div>

					<div className="absolute top-5 h-1 bg-gray-200 z-0" style={progressBarStyle}>
						<div className="absolute h-full transition-all" 
							style={{
								...progressStyle,
								backgroundColor: "var(--color-accent-active)", 
							}}
						/>
					</div>
				</div>

				{/* Step Content */}
				<div className="my-8 min-h-[150px]">
					{children && children[step - 1]}
				</div>

				<hr className="my-5 text-gray-200" />

				{/* Controls */}
				<div className="flex justify-between mt-8">
					<button className="px-6 py-2 rounded bg-gray-200 text-gray-600 font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300 transition"
						onClick={prevStep}
						disabled={isFirstStep}
					>
						← Previous
					</button>

					<button  className="px-6 py-2 rounded   bg-gray-200 text-gray-600 font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-300  transition" 
						onClick={nextStep}
						disabled={isDisabled} 
					>
						{isLastStep ? endButtonName : "Next"}{" "}
						{isLastStep ? "" : "→"}
					</button>
				</div>
			</div>
		);
	}
);

export default MultiStepForm;
