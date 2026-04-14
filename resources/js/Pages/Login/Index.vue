<script setup>
import Button from "@/Components/Common/Button.vue";
</script>

<template>
	<div class="login-page bg-background text-on-background min-h-screen selection:bg-secondary-container flex flex-col items-center justify-center p-4 md:p-8">
		<main class="w-full max-w-md">
			<!-- Branding (outside card for cleaner look) -->
			

			<!-- Login Card -->
			<div class="bg-surface-container-lowest p-8 md:p-12 rounded-xl shadow-xl shadow-on-surface/5 border border-outline-variant/20">
				<div class="mb-8 text-center">
                    <span class="text-2xl font-bold tracking-tighter text-on-surface uppercase">The Curated Canvas</span>
                </div>
                <div class="w-full">
                    <h1 class="text-xl font-semibold leading-tight tracking-tight text-on-surface mb-2">Welcome Back</h1>
					<p class="text-on-surface-variant text-sm mb-10">Enter your credentials to access your dashboard.</p>
					<form class="space-y-6" @submit.prevent>
						<!-- Email Input -->
						<div class="space-y-2">
							<label class="block text-[10px] uppercase tracking-widest font-bold text-on-surface-variant" for="email">Email Address</label>
							<input class="w-full h-12 px-4 bg-surface-container-low border border-outline-variant/30 rounded-sm focus:ring-2 focus:ring-primary-container transition-all text-on-surface placeholder:text-on-surface-variant/50" id="email" name="email" placeholder="Email" type="email" />
						</div>

						<!-- Password Input -->
						<div class="space-y-2">
							<div class="flex justify-between items-center">
								<label class="block text-[10px] uppercase tracking-widest font-bold text-on-surface-variant" for="password">Password</label>
								<a class="text-[10px] uppercase tracking-widest font-bold text-primary hover:text-on-primary-container transition-colors" href="#">Forgot Password?</a>
							</div>
							<input class="w-full h-12 px-4 bg-surface-container-low border border-outline-variant/30 rounded-sm transition-all text-on-surface placeholder:text-on-surface-variant/50" id="password" name="password" placeholder="Password" type="password" />
						</div>

						<!-- Primary Action -->
						<Button class="w-full h-12 flex items-center justify-center" type="submit">
							Sign In
						</Button>
					</form>
				</div>
			</div>

			<!-- Footer: Centered -->
			<footer class="mt-12 flex flex-col items-center gap-4 px-8 text-center">
				<span class="text-[10px] uppercase tracking-widest font-medium text-zinc-400">© 2024 The Curated Canvas. All rights reserved.</span>
				<div class="flex gap-4">
					<a class="text-[10px] uppercase tracking-widest font-medium text-zinc-400 hover:text-primary transition-colors" href="#">Privacy Policy</a>
					<a class="text-[10px] uppercase tracking-widest font-medium text-zinc-400 hover:text-primary transition-colors" href="#">Terms of Service</a>
					<a class="text-[10px] uppercase tracking-widest font-medium text-zinc-400 hover:text-primary transition-colors" href="#">Contact</a>
				</div>
			</footer>
		</main>
	</div>
</template>
